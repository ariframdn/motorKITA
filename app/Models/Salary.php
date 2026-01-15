<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $fillable = [
        'mechanic_id',
        'base_salary',
        'attendance_days',
        'daily_rate',
        'bonus_amount',
        'total_amount',
        'payment_method',
        'payment_proof',
        'status',
        'payment_date',
        'period_start',
        'period_end',
        'notes',
        'processed_by',
    ];

    protected function casts(): array
    {
        return [
            'base_salary' => 'decimal:2',
            'daily_rate' => 'decimal:2',
            'bonus_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'payment_date' => 'date',
            'period_start' => 'date',
            'period_end' => 'date',
        ];
    }

    public function mechanic()
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'payment_date' => now(),
        ]);
    }
}
