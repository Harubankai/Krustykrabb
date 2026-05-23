<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuItemController extends Controller
{
    public function index()
    {
        return response()->json(MenuItem::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string'
        ]);

        if (isset($data['image'])) {
            $data['image'] = str_replace(url('/'), '', $data['image']);
            $data['image'] = ltrim($data['image'], '/');
        }

        $item = MenuItem::create($data);
        return response()->json(['success' => true, 'item' => $item]);
    }

    public function update(Request $request, $id)
    {
        $item = MenuItem::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:50',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string'
        ]);

        if (isset($data['image'])) {
            $data['image'] = str_replace(url('/'), '', $data['image']);
            $data['image'] = ltrim($data['image'], '/');
        }

        $item->update($data);
        return response()->json(['success' => true, 'item' => $item]);
    }

    public function destroy($id)
    {
        $item = MenuItem::findOrFail($id);
        $item->delete();
        return response()->json(['success' => true]);
    }

    public function mostSold()
    {
        $mostSold = MenuItem::select(
            'menu_items.name',
            'menu_items.image',
            'menu_items.price',
            \Illuminate\Support\Facades\DB::raw('SUM(order_items.quantity) as total_sold')
        )
            ->join('order_items', 'menu_items.name', '=', 'order_items.name')
            ->groupBy('menu_items.name', 'menu_items.image', 'menu_items.price')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();

        // If no sales yet, return some random menu items to fill the advertisement
        if ($mostSold->isEmpty()) {
            $mostSold = MenuItem::inRandomOrder()->take(5)->get()->map(function ($item) {
                return [
                    'name' => $item->name,
                    'image' => $item->image,
                    'price' => $item->price,
                    'total_sold' => 0
                ];
            });
        }

        return response()->json($mostSold);
    }
}
