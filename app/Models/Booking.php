<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'customer_id',
        'vehicle_id',
        'mechanic_id',
        'service_type',
        'booking_date',
        'status',
        'cost',
        'notes',
        'payment_status',
        'payment_method',
        'payment_proof',
        'promo_code_id',
        'discount_amount',
        'hpp_cost',
        'final_cost',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'cost' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'hpp_cost' => 'decimal:2',
            'final_cost' => 'decimal:2',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function mechanic()
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function servicePrice()
    {
        return $this->belongsTo(ServicePrice::class, 'service_type', 'service_name');
    }

    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}