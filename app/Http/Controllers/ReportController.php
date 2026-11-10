<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\StockTransaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $materials = Service::with('category')->orderBy('title')->get();
        $lowStock = $materials->filter(fn (Service $material) => $material->isLowStock());
        $totalValue = $materials->sum(fn (Service $material) => $material->inventoryValue());

        $query = StockTransaction::with(['service.category', 'user'])->latest('transaction_date')->latest('id');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->get();

        return view('backend.reports.index', compact('materials', 'lowStock', 'totalValue', 'transactions'));
    }
}
