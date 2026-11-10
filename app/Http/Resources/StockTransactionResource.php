<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'material' => $this->service?->title,
            'category' => $this->service?->category?->title,
            'unit' => $this->service?->unit,
            'type' => $this->type,
            'type_label' => $this->typeLabel(),
            'quantity' => (float) $this->quantity,
            'balance_after' => (float) $this->balance_after,
            'transaction_date' => optional($this->transaction_date)->toDateString(),
            'party_name' => $this->party_name,
            'party_details' => $this->party_details,
            'notes' => $this->notes,
            'user' => $this->user?->name,
        ];
    }
}
