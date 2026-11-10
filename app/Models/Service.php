<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function employees()
    {
        return $this->belongsToMany(Employee::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function isLowStock(): bool
    {
        return (float) $this->quantity <= (float) $this->min_stock_level;
    }

    public function inventoryValue(): float
    {
        return (float) $this->quantity * (float) $this->price;
    }
}
