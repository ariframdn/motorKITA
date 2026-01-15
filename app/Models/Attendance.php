<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'mechanic_id',
        'attendance_code_id',
        'date',
        'check_in_time',
        'check_out_time',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function mechanic()
    {
        return $this->belongsTo(User::class, 'mechanic_id');
    }

    public function attendanceCode()
    {
        return $this->belongsTo(AttendanceCode::class);
    }

    public function getWorkHoursAttribute()
    {
        if ($this->check_in_time && $this->check_out_time) {
            try {
                $checkIn = \Carbon\Carbon::createFromTimeString($this->check_in_time);
                $checkOut = \Carbon\Carbon::createFromTimeString($this->check_out_time);
                return $checkOut->diffInHours($checkIn);
            } catch (\Exception $e) {
                return 0;
            }
        }
        return 0;
    }
}
