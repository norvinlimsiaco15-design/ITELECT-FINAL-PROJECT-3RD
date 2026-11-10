<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryReportItem extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:3',
            'unit_price' => 'decimal:2',
            'min_stock_level' => 'decimal:3',
            'line_value' => 'decimal:2',
            'is_low_stock' => 'boolean',
        ];
    }

    public function report(): BelongsTo
    {
        return $this->belongsTo(InventoryReport::class, 'inventory_report_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
