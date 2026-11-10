<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'report_date' => optional($this->report_date)->toDateString(),
            'notes' => $this->notes,
            'status' => $this->status,
            'submitted_at' => optional($this->submitted_at)->toIso8601String(),
            'user_id' => $this->user_id,
            'prepared_by' => $this->user?->name,
            'total_value' => (float) $this->items->sum('line_value'),
            'low_stock_count' => $this->items->where('is_low_stock', true)->count(),
            'items' => $this->when($this->relationLoaded('items'), function () {
                return $this->items->map(fn ($item) => [
                    'id' => $item->id,
                    'service_id' => $item->service_id,
                    'material_name' => $item->material_name,
                    'category_name' => $item->category_name,
                    'unit' => $item->unit,
                    'quantity' => (float) $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'min_stock_level' => (float) $item->min_stock_level,
                    'line_value' => (float) $item->line_value,
                    'is_low_stock' => (bool) $item->is_low_stock,
                ]);
            }),
        ];
    }
}
