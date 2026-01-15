<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\AttendanceCode;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttendanceController extends Controller
{
    /**
     * Show attendance page for mechanic
     */
    public function index()
    {
        $todayCode = AttendanceCode::where('date', today())
            ->where('is_used', false)
            ->first();

        $todayAttendance = null;
        if (auth()->user()->isMechanic()) {
            $todayAttendance = Attendance::where('mechanic_id', auth()->id())
                ->where('date', today())
                ->first();
        }

        return view('mechanic.attendance', compact('todayCode', 'todayAttendance'));
    }

    /**
     * Check in with code (Mechanic)
     */
    public function checkIn(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:8',
        ]);

        $code = strtoupper($request->code);
        $attendanceCode = AttendanceCode::where('code', $code)
            ->where('date', today())
            ->where('is_used', false)
            ->first();

        if (!$attendanceCode) {
            return redirect()->back()->with('error', 'Kode absen tidak valid atau sudah digunakan!');
        }

        // Check if already checked in today
        $existingAttendance = Attendance::where('mechanic_id', auth()->id())
            ->where('date', today())
            ->first();

        if ($existingAttendance) {
            return redirect()->back()->with('error', 'Anda sudah absen hari ini!');
        }

        // Create attendance record
        $attendance = Attendance::create([
            'mechanic_id' => auth()->id(),
            'attendance_code_id' => $attendanceCode->id,
            'date' => today(),
            'check_in_time' => now()->format('H:i:s'),
        ]);

        // Mark code as used
        $attendanceCode->markAsUsed();

        // Create notification
        NotificationController::create(
            auth()->id(),
            'attendance',
            'Absen Berhasil',
            'Anda berhasil absen masuk hari ini.',
            'App\Models\Attendance',
            $attendance->id
        );

        // Notify admin
        $admin = User::where('role', 'admin')->first();
        if ($admin) {
            NotificationController::create(
                $admin->id,
                'attendance',
                'Absen Mekanik',
                auth()->user()->name . ' telah absen masuk hari ini.',
                'App\Models\Attendance',
                $attendance->id
            );
        }

        return redirect()->back()->with('success', 'Absen berhasil!');
    }

    /**
     * Check out (Mechanic)
     */
    public function checkOut()
    {
        $attendance = Attendance::where('mechanic_id', auth()->id())
            ->where('date', today())
            ->whereNull('check_out_time')
            ->first();

        if (!$attendance) {
            return redirect()->back()->with('error', 'Anda belum absen masuk hari ini!');
        }

        $attendance->update([
            'check_out_time' => now()->format('H:i:s'),
        ]);

        return redirect()->back()->with('success', 'Check out berhasil!');
    }

    /**
     * Generate attendance code (Admin only)
     */
    public function generateCode(Request $request)
    {
        $code = AttendanceCode::generateCode();
        
        // Ensure unique code
        while (AttendanceCode::where('code', $code)->where('date', today())->exists()) {
            $code = AttendanceCode::generateCode();
        }

        $attendanceCode = AttendanceCode::create([
            'code' => $code,
            'date' => today(),
            'created_by' => auth()->id(),
        ]);

        // Notify all mechanics
        $mechanics = User::where('role', 'mechanic')->get();
        foreach ($mechanics as $mechanic) {
            NotificationController::create(
                $mechanic->id,
                'attendance_code',
                'Kode Absen Baru',
                'Kode absen hari ini: ' . $code,
                'App\Models\AttendanceCode',
                $attendanceCode->id
            );
        }

        return redirect()->back()->with('success', 'Kode absen berhasil dibuat: ' . $code);
    }

    /**
     * List attendance codes (Admin)
     */
    public function codes()
    {
        $codes = AttendanceCode::with('creator')
            ->latest()
            ->paginate(20);

        return view('admin.attendance-codes', compact('codes'));
    }

    /**
     * List attendances (Admin)
     */
    public function attendances(Request $request)
    {
        $query = Attendance::with(['mechanic', 'attendanceCode']);

        if ($request->has('date')) {
            $query->where('date', $request->date);
        }

        if ($request->has('mechanic_id')) {
            $query->where('mechanic_id', $request->mechanic_id);
        }

        $attendances = $query->latest()->paginate(20);
        $mechanics = User::where('role', 'mechanic')->get();

        return view('admin.attendances', compact('attendances', 'mechanics'));
    }
}
