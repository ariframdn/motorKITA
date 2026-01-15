<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
        ];
    }

    // Relationships
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'customer_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

    public function assignedBookings()
    {
        return $this->hasMany(Booking::class, 'mechanic_id');
    }

    public function approvedPayments()
    {
        return $this->hasMany(Payment::class, 'approved_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'mechanic_id');
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class, 'mechanic_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'customer_id');
    }

    public function mechanicReviews()
    {
        return $this->hasMany(Review::class, 'mechanic_id');
    }

    public function createdAttendanceCodes()
    {
        return $this->hasMany(AttendanceCode::class, 'created_by');
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isMechanic()
    {
        return $this->role === 'mechanic';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }
}