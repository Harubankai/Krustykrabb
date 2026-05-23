<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;

class AdminController extends Controller
{
    // =========================
    // Get total riders
    // =========================
    public function totalRiders()
    {
        $count = User::where('role', 'rider')->count();

        return response()->json($count);
    }

    // =========================
    // Get monthly sales (Jan → current month)
    // =========================
    public function monthlySales()
    {
        // Get total sales grouped by month for current year
        $sales = Order::selectRaw('MONTH(created_at) as month, SUM(total_price) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->pluck('total', 'month'); // returns array: [1 => 125345.50, 2 => 138220.75, ...]

        return response()->json($sales);
    }

}
