<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'customer_id',
        'brand',
        'model',
        'plate_number',
        'last_service_date',
    ];

    protected function casts(): array
    {
        return [
            'last_service_date' => 'date',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}