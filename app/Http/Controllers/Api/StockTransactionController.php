<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StockTransactionRequest;
use App\Http\Resources\StockTransactionResource;
use App\Models\StockTransaction;
use App\Services\StockMovementService;
use Illuminate\Http\Request;

class StockTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = StockTransaction::with(['service.category', 'user'])
            ->latest('transaction_date')
            ->latest('id');

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('service_id')) {
            $query->where('service_id', $request->integer('service_id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date('date_to'));
        }

        if ($request->filled('q')) {
            $term = $request->string('q');
            $query->where(function ($q) use ($term) {
                $q->where('party_name', 'like', "%{$term}%")
                    ->orWhere('party_details', 'like', "%{$term}%")
                    ->orWhere('notes', 'like', "%{$term}%")
                    ->orWhereHas('service', function ($serviceQuery) use ($term) {
                        $serviceQuery->where('title', 'like', "%{$term}%");
                    });
            });
        }

        return StockTransactionResource::collection($query->get());
    }

    public function store(StockTransactionRequest $request, StockMovementService $stock)
    {
        $transaction = $stock->record($request->validated(), $request->user()->id);

        return (new StockTransactionResource($transaction->load(['service.category', 'user'])))
            ->response()
            ->setStatusCode(201);
    }
}
