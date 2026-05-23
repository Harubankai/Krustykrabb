<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Message;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Common mapper so frontend JS doesn't break
    private function mapOrder($o)
    {
        return [
            'db_id' => $o->id,
            'id' => $o->order_id,
            'status' => $o->status,
            'deliveryStep' => $o->delivery_step,
            'totalItems' => collect($o->items)->sum('qty'),
            'totalPrice' => $o->total,
            'customer' => [
                'name' => $o->customer->name ?? '',
                'address' => $o->customer->address ?? '',
                'phone' => $o->customer->phone ?? ''
            ],
            'rider' => $o->rider ? [
                'name' => $o->rider->name,
                'email' => $o->rider->email,
                'phone' => $o->rider->phone,
            ] : null,
            'items' => $o->items->map(fn($i) => [
                'name' => $i->name,
                'qty' => $i->quantity,
                'price' => $i->price,
            ]),
            'statusTimestamps' => [
                'placedAt' => $o->created_at,
                'acceptedAt' => $o->accepted_at,
                'pickedUpAt' => $o->picked_up_at,
                'inTransitAt' => $o->in_transit_at,
                'arrivedAt' => $o->arrived_at,
                'completedAt' => $o->completed_at,
            ],
            'createdAt' => $o->created_at,
        ];
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'order_id' => 'required|string',
            'total_items' => 'required|integer',
            'total_price' => 'required|numeric',
            'payment_method' => 'required|string',
            'items' => 'required|array',
        ]);

        try {
            $user = \App\Models\User::find(session('user')->id ?? 1);
            
            if (!$user || !$user->phone || !$user->address) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Please complete your profile (Phone and Address) before ordering. You can update your details in the Profile section.'
                ], 400);
            }

            $order = Order::create([
                'order_id' => $data['order_id'],
                'customer_id' => session('user')->id ?? 1,
                'total' => $data['total_price'],
                'total_items' => $data['total_items'],
                'payment_method' => $data['payment_method'],
                'status' => 'Preparing',
            ]);

            foreach ($data['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'name' => $item['name'] ?? 'Item',
                    'price' => $item['price'] ?? 0,
                    'quantity' => $item['qty'] ?? 1,
                ]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Customer Side
    public function myOrders()
    {
        $userId = session('user')->id ?? null;
        if (!$userId) return response()->json([]);

        $orders = Order::with(['items', 'rider', 'customer'])
            ->where('customer_id', $userId)
            ->offset(0)->limit(50)->get();
            
        return response()->json($orders->map(fn($o) => $this->mapOrder($o)));
    }

    // Rider Side
    public function availableOrders()
    {
        $riderId = session('user')->id ?? null;
        if (!$riderId) return response()->json([]);

        $orders = Order::with(['items', 'customer', 'rider'])
            ->where(function ($query) use ($riderId) {
                $query->whereNull('rider_id')->where('status', 'Preparing')
                      ->orWhere('rider_id', $riderId);
            })
            ->whereNotIn('status', ['Completed', 'Cancelled'])
            ->get();

        return response()->json($orders->map(fn($o) => $this->mapOrder($o)));
    }

    public function acceptOrder($dbId)
    {
        $riderId = session('user')->id ?? null;
        if (!$riderId) return response()->json(['success' => false, 'message' => 'Unauthorized']);

        $rider = \App\Models\User::find($riderId);
        if (!$rider || !in_array($rider->status, ['Online', 'On Delivery'])) {
            return response()->json(['success' => false, 'message' => 'You must be Online to accept orders.']);
        }

        $order = Order::find($dbId);
        if (!$order || $order->rider_id !== null) {
            return response()->json(['success' => false, 'message' => 'Order taken']);
        }

        $order->update([
            'rider_id' => $riderId,
            'status' => 'Accepted',
            'delivery_step' => 1,
            'accepted_at' => now(),
        ]);

        $rider->update(['status' => 'On Delivery', 'last_seen' => now()]);

        return response()->json(['success' => true]);
    }

    public function updateStatus($dbId, Request $request)
    {
        $riderId = session('user')->id ?? null;
        $order = Order::where('id', $dbId)->where('rider_id', $riderId)->first();
        if (!$order) return response()->json(['success' => false]);

        $updates = [];
        if ($request->has('status')) $updates['status'] = $request->input('status');
        if ($request->has('delivery_step')) $updates['delivery_step'] = $request->input('delivery_step');
        
        $s = $request->input('status');
        if ($s === 'In Transit') $updates['in_transit_at'] = now();
        if ($s === 'Arrived') $updates['arrived_at'] = now();
        if ($s === 'Completed') {
            $updates['completed_at'] = now();
            
            if ($request->hasFile('delivery_photo')) {
                $file = $request->file('delivery_photo');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('delivery_photos', $filename, 'public');
                $updates['delivery_photo'] = $path;
            }

            $order->update($updates);

            $remaining = Order::where('rider_id', $riderId)->whereNotIn('status', ['Completed', 'Cancelled'])->count();
            if ($remaining === 0) {
                \App\Models\User::where('id', $riderId)->update(['status' => 'Online', 'last_seen' => now()]);
            }
        } else {
            $order->update($updates);
        }

        return response()->json(['success' => true]);
    }

    // Admin Stats
    public function adminStatistics(Request $request)
    {
        $currentYear = date('Y');
        $selectedYear = $request->query('year', $currentYear);
        $currentMonth = ($selectedYear == $currentYear) ? (int)date('n') : 12;

        // Monthly Sales Array (0-indexed for JS charts)
        $monthlySales = array_fill(1, 12, 0); 
        $orders = Order::whereYear('created_at', $selectedYear)
            ->whereNotIn('status', ['Cancelled', 'canceled'])
            ->get();

        foreach($orders as $order) {
            $m = (int)$order->created_at->format('n');
            $monthlySales[$m] += $order->total;
        }

        $monthlySalesArray = [];
        for ($i = 1; $i <= $currentMonth; $i++) {
            $monthlySalesArray[] = $monthlySales[$i];
        }

        // Top Selling Items (This month)
        $topItemsHash = [];
        $items = OrderItem::whereHas('order', function($q) use ($selectedYear, $currentMonth) {
            $q->whereYear('created_at', $selectedYear)
              ->whereMonth('created_at', $currentMonth)
              ->whereNotIn('status', ['Cancelled', 'canceled']);
        })->get();

        foreach($items as $item) {
            if (!isset($topItemsHash[$item->name])) {
                $topItemsHash[$item->name] = 0;
            }
            $topItemsHash[$item->name] += $item->quantity;
        }

        arsort($topItemsHash);
        $topItemsHash = array_slice($topItemsHash, 0, 5, true);

        $topItemsArray = [];
        foreach($topItemsHash as $name => $qty) {
            $topItemsArray[] = [$name, $qty];
        }

        // Unread messages count
        $unreadMessagesCount = Message::where('is_read', false)->count();

        // Recent messages (last 3)
        $recentMessages = Message::orderBy('created_at', 'desc')
            ->limit(3)
            ->get(['name', 'message', 'created_at']);

        return response()->json([
            'monthlySales' => $monthlySalesArray,
            'topItems' => $topItemsArray,
            'unreadMessagesCount' => $unreadMessagesCount,
            'recentMessages' => $recentMessages
        ]);
    }

    // Admin Side
    public function allOrders()
    {
        $orders = Order::with(['items', 'customer', 'rider'])
            ->latest()
            ->get();
            
        return response()->json($orders->map(function($o) {
            $mapped = $this->mapOrder($o);
            $mapped['delivery_photo'] = $o->delivery_photo ? asset('storage/' . $o->delivery_photo) : null;
            return $mapped;
        }));
    }
    // Customer cancels an order (allowed in Preparing or Accepted status)
    public function cancelOrder($dbId)
    {
        $userId = session('user')->id ?? null;
        if (!$userId) return response()->json(['success' => false, 'message' => 'Unauthorized']);

        $order = Order::where('id', $dbId)->where('customer_id', $userId)->first();
        if (!$order) return response()->json(['success' => false, 'message' => 'Order not found']);

        if (!in_array($order->status, ['Preparing', 'Accepted'])) {
            return response()->json(['success' => false, 'message' => 'Order can only be cancelled while it is Preparing or Accepted.']);
        }

        if ($order->rider_id) {
            \App\Models\User::where('id', $order->rider_id)->update([
                'status' => 'Online',
                'last_seen' => now()
            ]);
        }

        $order->update([
            'status' => 'Cancelled',
            'delivery_step' => 0
        ]);
        return response()->json(['success' => true]);
    }

    // Rider unaccepts an order (only allowed in Accepted/step-1, before pickup)
    public function unacceptOrder($dbId)
    {
        $riderId = session('user')->id ?? null;
        if (!$riderId) return response()->json(['success' => false, 'message' => 'Unauthorized']);

        $order = Order::where('id', $dbId)->where('rider_id', $riderId)->first();
        if (!$order) return response()->json(['success' => false, 'message' => 'Order not found']);

        if ($order->status !== 'Accepted' || $order->delivery_step > 1) {
            return response()->json(['success' => false, 'message' => 'Order can only be unaccepted before pickup.']);
        }

        $order->update([
            'rider_id'      => null,
            'status'        => 'Preparing',
            'delivery_step' => 0,
            'accepted_at'   => null,
        ]);

        $remaining = Order::where('rider_id', $riderId)->whereNotIn('status', ['Completed', 'Cancelled'])->count();
        if ($remaining === 0) {
            \App\Models\User::where('id', $riderId)->update(['status' => 'Online', 'last_seen' => now()]);
        }

        return response()->json(['success' => true]);
    }
}
