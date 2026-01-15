<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCode extends Model
{
    protected $fillable = [
        'code',
        'date',
        'is_used',
        'used_at',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'is_used' => 'boolean',
            'used_at' => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class);
    }

    public function markAsUsed()
    {
        $this->update([
            'is_used' => true,
            'used_at' => now(),
        ]);
    }

    public static function generateCode()
    {
        return strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
    }
}
