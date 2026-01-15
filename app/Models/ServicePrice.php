<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicePrice extends Model
{
    protected $fillable = [
        'service_name',
        'description',
        'price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'service_type', 'service_name');
    }

    public function hppItems()
    {
        return $this->hasMany(ServiceHppItem::class);
    }

    public function getTotalHppAttribute()
    {
        return $this->hppItems->sum('total_cost');
    }
}
