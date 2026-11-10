<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category' => $this->category?->title,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'unit' => $this->unit,
            'quantity' => (float) $this->quantity,
            'min_stock_level' => (float) $this->min_stock_level,
            'price' => (float) $this->price,
            'inventory_value' => $this->inventoryValue(),
            'is_low_stock' => $this->isLowStock(),
            'status' => (bool) $this->status,
        ];
    }
}
