<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MaterialResource;
use App\Http\Resources\StockTransactionResource;
use App\Models\Service;
use App\Models\StockTransaction;

class DashboardController extends Controller
{
    public function index()
    {
        $materials = Service::with('category')->orderBy('title')->get();
        $lowStock = $materials->filter(fn (Service $material) => $material->isLowStock())->values();
        $recent = StockTransaction::with(['service.category', 'user'])
            ->latest('transaction_date')
            ->latest('id')
            ->take(10)
            ->get();

        return response()->json([
            'materials_count' => $materials->count(),
            'total_value' => round($materials->sum(fn (Service $material) => $material->inventoryValue()), 2),
            'low_stock_count' => $lowStock->count(),
            'recent_count' => $recent->count(),
            'stock_levels' => MaterialResource::collection($materials),
            'low_stock' => MaterialResource::collection($lowStock),
            'recent_transactions' => StockTransactionResource::collection($recent),
        ]);
    }
}
