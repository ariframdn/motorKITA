<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceHppItem extends Model
{
    protected $fillable = [
        'service_price_id',
        'inventory_id',
        'quantity',
        'unit_price',
        'total_cost',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_cost' => 'decimal:2',
        ];
    }

    public function servicePrice()
    {
        return $this->belongsTo(ServicePrice::class);
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class);
    }

    public function calculateTotalCost()
    {
        $this->total_cost = $this->unit_price * $this->quantity;
        return $this->total_cost;
    }
}
