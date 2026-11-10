<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\MaterialResource;
use App\Http\Resources\StockTransactionResource;
use App\Models\Service;
use App\Models\StockTransaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function valuation(Request $request)
    {
        $materials = Service::with('category')->orderBy('title')->get();
        $lowStock = $materials->filter(fn (Service $material) => $material->isLowStock())->values();

        $query = StockTransaction::with(['service.category', 'user'])->latest('transaction_date')->latest('id');

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date('date_to'));
        }

        return response()->json([
            'total_value' => round($materials->sum(fn (Service $material) => $material->inventoryValue()), 2),
            'materials_count' => $materials->count(),
            'low_stock_count' => $lowStock->count(),
            'materials' => MaterialResource::collection($materials),
            'low_stock' => MaterialResource::collection($lowStock),
            'transactions' => StockTransactionResource::collection($query->get()),
        ]);
    }

    public function lowStock()
    {
        $materials = Service::with('category')
            ->orderBy('title')
            ->get()
            ->filter(fn (Service $material) => $material->isLowStock())
            ->values();

        return MaterialResource::collection($materials);
    }
}
