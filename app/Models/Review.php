<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'booking_id',
        'customer_id',
        'mechanic_id',
        'review_type',
        'rating_workshop',
        'rating_mechanic',
        'comment',
        'photo',
    ];

    protected function casts(): array
    {
        return [
            'rating_workshop' => 'integer',
            'rating_mechanic' => 'integer',
        ];
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function mechanic()
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }

    public function getAverageRatingAttribute()
    {
        $ratings = array_filter([$this->rating_workshop, $this->rating_mechanic]);
        return count($ratings) > 0 ? round(array_sum($ratings) / count($ratings), 2) : 0;
    }
}
