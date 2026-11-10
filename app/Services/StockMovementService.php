<?php

namespace App\Services;

use App\Models\Service;
use App\Models\StockTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockMovementService
{
    public function record(array $data, ?int $userId): StockTransaction
    {
        return DB::transaction(function () use ($data, $userId) {
            $material = Service::lockForUpdate()->findOrFail($data['service_id']);
            $current = (float) $material->quantity;
            $qty = abs((float) $data['quantity']);
            $direction = $data['adjustment_direction'] ?? null;

            $signedQuantity = match ($data['type']) {
                'in' => $qty,
                'out' => $qty,
                'adjustment' => $direction === 'decrease' ? -$qty : $qty,
                default => throw ValidationException::withMessages([
                    'type' => 'Choose stock-in, stock-out, or adjustment.',
                ]),
            };

            $newBalance = match ($data['type']) {
                'in' => $current + $qty,
                'out' => $current - $qty,
                'adjustment' => $current + $signedQuantity,
            };

            if ($newBalance < 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'This movement cannot exceed available quantity ('.$current.' '.$material->unit.').',
                ]);
            }

            $material->quantity = $newBalance;
            $material->save();

            return StockTransaction::create([
                'service_id' => $material->id,
                'user_id' => $userId,
                'type' => $data['type'],
                'quantity' => $signedQuantity,
                'balance_after' => $newBalance,
                'transaction_date' => $data['transaction_date'],
                'party_name' => $data['party_name'] ?? null,
                'party_details' => $data['party_details'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    public function recordOpeningBalance(Service $material, ?int $userId): void
    {
        if ((float) $material->quantity <= 0) {
            return;
        }

        StockTransaction::create([
            'service_id' => $material->id,
            'user_id' => $userId,
            'type' => 'in',
            'quantity' => $material->quantity,
            'balance_after' => $material->quantity,
            'transaction_date' => now()->toDateString(),
            'party_name' => 'Opening balance',
            'notes' => 'Opening quantity recorded when the material was added.',
        ]);
    }
}
