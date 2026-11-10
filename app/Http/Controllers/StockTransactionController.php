<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\StockTransaction;
use App\Services\StockMovementService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StockTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransaction::with(['service.category', 'user'])->latest('transaction_date')->latest('id');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->service_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(function ($q) use ($term) {
                $q->where('party_name', 'like', "%{$term}%")
                    ->orWhere('party_details', 'like', "%{$term}%")
                    ->orWhere('notes', 'like', "%{$term}%")
                    ->orWhereHas('service', function ($serviceQuery) use ($term) {
                        $serviceQuery->where('title', 'like', "%{$term}%");
                    });
            });
        }

        $transactions = $query->get();
        $materials = Service::orderBy('title')->get();

        return view('backend.stock.index', compact('transactions', 'materials'));
    }

    public function create()
    {
        $materials = Service::where('status', 1)->orderBy('title')->get();

        return view('backend.stock.create', compact('materials'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_id' => 'required|exists:services,id',
            'type' => 'required|in:in,out,adjustment',
            'adjustment_direction' => 'required_if:type,adjustment|nullable|in:increase,decrease',
            'quantity' => 'required|numeric|gt:0',
            'transaction_date' => 'required|date',
            'party_name' => 'required_if:type,in,out|nullable|string|max:255',
            'party_details' => 'nullable|string',
            'notes' => 'nullable|string',
        ], [
            'quantity.gt' => 'Quantity must be greater than zero.',
            'party_name.required_if' => 'Enter the supplier for stock-in, or the customer for stock-out.',
            'adjustment_direction.required_if' => 'Choose whether the adjustment increases or decreases stock.',
        ]);

        try {
            app(StockMovementService::class)->record($data, auth()->id());
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('stock-transaction.index')->with('success', 'Stock transaction recorded and inventory updated.');
    }
}
