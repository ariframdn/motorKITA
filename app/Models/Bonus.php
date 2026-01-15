<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    protected $fillable = [
        'name',
        'description',
        'type',
        'min_services',
        'bonus_amount',
        'bonus_type',
        'effective_date',
        'expiry_date',
        'is_active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'bonus_amount' => 'decimal:2',
            'effective_date' => 'date',
            'expiry_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isApplicable($serviceCount, $date = null)
    {
        if (!$this->is_active) {
            return false;
        }

        $date = $date ?? now();
        if ($this->effective_date && $date->lt($this->effective_date)) {
            return false;
        }
        if ($this->expiry_date && $date->gt($this->expiry_date)) {
            return false;
        }

        if ($this->min_services && $serviceCount < $this->min_services) {
            return false;
        }

        return true;
    }

    public function calculateBonus($baseAmount = 0)
    {
        if ($this->bonus_type === 'percentage') {
            return ($baseAmount * $this->bonus_amount) / 100;
        }
        return $this->bonus_amount;
    }
}
