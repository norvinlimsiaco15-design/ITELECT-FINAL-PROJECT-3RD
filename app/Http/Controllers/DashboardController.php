<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\StockTransaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $materials = Service::with('category')->orderBy('title')->get();
        $lowStock = $materials->filter(fn (Service $material) => $material->isLowStock())->values();
        $totalValue = $materials->sum(fn (Service $material) => $material->inventoryValue());
        $recentTransactions = StockTransaction::with(['service', 'user'])
            ->latest('transaction_date')
            ->latest('id')
            ->take(10)
            ->get();

        return view('backend.dashboard.index', compact(
            'materials',
            'lowStock',
            'totalValue',
            'recentTransactions'
        ));
    }

    public function updateStatus(Request $request)
    {
        return back();
    }
}
