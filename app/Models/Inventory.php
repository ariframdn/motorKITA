<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    protected $table = 'inventory';

    protected $fillable = [
        'part_name',
        'quantity',
        'price',
        'low_stock_level',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function isLowStock()
    {
        return $this->quantity <= $this->low_stock_level;
    }

    public function serviceHppItems()
    {
        return $this->hasMany(ServiceHppItem::class);
    }
}