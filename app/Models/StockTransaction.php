<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $guarded = [];

    protected $casts = [
        'transaction_date' => 'date',
        'quantity' => 'decimal:3',
        'balance_after' => 'decimal:3',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'in' => 'Stock In',
            'out' => 'Stock Out',
            'adjustment' => 'Adjustment',
            default => ucfirst((string) $this->type),
        };
    }
}
