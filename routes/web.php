<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\MechanicController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\ServiceHppController;
use App\Http\Controllers\FinancialController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

// Auth Routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Profile Routes (All authenticated users)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/{id}', [ProfileController::class, 'view'])->name('profile.view');
    
    // Notifications (All roles) - API endpoints
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/count', [NotificationController::class, 'unreadCount'])->name('notifications.count');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    
    // Broadcasting authentication
    Broadcast::routes(['middleware' => ['web', 'auth']]);
});

// Customer Routes
Route::middleware(['auth', 'role:customer'])->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [CustomerController::class, 'dashboard'])->name('dashboard');
    Route::get('/booking', [CustomerController::class, 'booking'])->name('booking');
    Route::post('/booking', [CustomerController::class, 'storeBooking'])->name('booking.store');
    Route::get('/history', [CustomerController::class, 'history'])->name('history');
    Route::post('/vehicle', [CustomerController::class, 'addVehicle'])->name('vehicle.add');
    Route::post('/payment/{bookingId}', [PaymentController::class, 'submitPayment'])->name('payment.submit');
    
    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/mechanic/{id}', [ReviewController::class, 'mechanicReviews'])->name('reviews.mechanic');
    
    // Promo codes
    Route::post('/promo-codes/validate', [PromoCodeController::class, 'validateCode'])->name('promo-codes.validate');
});

// Mechanic Routes
Route::middleware(['auth', 'role:mechanic'])->prefix('mechanic')->name('mechanic.')->group(function () {
    Route::get('/dashboard', [MechanicController::class, 'dashboard'])->name('dashboard');
    Route::get('/tasks', [MechanicController::class, 'tasks'])->name('tasks');
    Route::patch('/tasks/{id}', [MechanicController::class, 'updateTask'])->name('tasks.update');
    Route::post('/report/{id}', [MechanicController::class, 'submitReport'])->name('report');
    
    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.check-out');
    
    // Earnings & Salaries
    Route::get('/earnings', [MechanicController::class, 'earnings'])->name('earnings');
    Route::get('/salaries', [MechanicController::class, 'salaries'])->name('salaries');
    Route::get('/salaries/{id}', [SalaryController::class, 'show'])->name('salaries.show');
    
    // Reviews
    Route::get('/reviews', [MechanicController::class, 'reviews'])->name('reviews');
});

// Admin Routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Inventory
    Route::get('/inventory', [AdminController::class, 'inventory'])->name('inventory');
    Route::post('/inventory', [AdminController::class, 'storeInventory'])->name('inventory.store');
    Route::patch('/inventory/{id}', [AdminController::class, 'updateInventory'])->name('inventory.update');
    Route::delete('/inventory/{id}', [AdminController::class, 'deleteInventory'])->name('inventory.delete');
    
    // Bookings
    Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings');
    Route::post('/bookings/{id}/assign', [AdminController::class, 'assignMechanic'])->name('bookings.assign');
    Route::get('/billing/{id}', [AdminController::class, 'billing'])->name('billing');
    Route::post('/billing/{id}', [AdminController::class, 'updateBilling'])->name('billing.update');
    
    // Service Prices
    Route::get('/service-prices', [AdminController::class, 'servicePrices'])->name('service-prices');
    Route::post('/service-prices', [AdminController::class, 'storeServicePrice'])->name('service-prices.store');
    Route::patch('/service-prices/{id}', [AdminController::class, 'updateServicePrice'])->name('service-prices.update');
    Route::delete('/service-prices/{id}', [AdminController::class, 'deleteServicePrice'])->name('service-prices.delete');
    
    // Payments
    Route::get('/payments', [AdminController::class, 'payments'])->name('payments');
    Route::post('/payments/{id}/approve', [PaymentController::class, 'approvePayment'])->name('payments.approve');
    Route::post('/payments/{id}/reject', [PaymentController::class, 'rejectPayment'])->name('payments.reject');
    
    // Attendance Management
    Route::get('/attendance-codes', [AttendanceController::class, 'codes'])->name('attendance-codes');
    Route::post('/attendance-codes/generate', [AttendanceController::class, 'generateCode'])->name('attendance-codes.generate');
    Route::get('/attendances', [AttendanceController::class, 'attendances'])->name('attendances');
    
    // Financial Reports
    Route::get('/financial', [FinancialController::class, 'index'])->name('financial');
    Route::get('/financial/chart-data', [FinancialController::class, 'getChartData'])->name('financial.chart-data');
    
    // Salaries
    Route::get('/salaries', [SalaryController::class, 'index'])->name('salaries');
    Route::post('/salaries', [SalaryController::class, 'create'])->name('salaries.create');
    Route::post('/salaries/{id}/paid', [SalaryController::class, 'markAsPaid'])->name('salaries.mark-paid');
    
    // Bonuses
    Route::get('/bonuses', [BonusController::class, 'index'])->name('bonuses');
    Route::post('/bonuses', [BonusController::class, 'store'])->name('bonuses.store');
    Route::patch('/bonuses/{id}', [BonusController::class, 'update'])->name('bonuses.update');
    Route::delete('/bonuses/{id}', [BonusController::class, 'destroy'])->name('bonuses.delete');
    
    // Promo Codes
    Route::get('/promo-codes', [PromoCodeController::class, 'index'])->name('promo-codes');
    Route::post('/promo-codes', [PromoCodeController::class, 'store'])->name('promo-codes.store');
    Route::patch('/promo-codes/{id}', [PromoCodeController::class, 'update'])->name('promo-codes.update');
    Route::delete('/promo-codes/{id}', [PromoCodeController::class, 'destroy'])->name('promo-codes.delete');
    
    // Service HPP
    Route::get('/service-hpp/{serviceId}', [ServiceHppController::class, 'index'])->name('service-hpp.index');
    Route::post('/service-hpp', [ServiceHppController::class, 'store'])->name('service-hpp.store');
    Route::patch('/service-hpp/{id}', [ServiceHppController::class, 'update'])->name('service-hpp.update');
    Route::delete('/service-hpp/{id}', [ServiceHppController::class, 'destroy'])->name('service-hpp.delete');
});