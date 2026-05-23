<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// ----------------------------------------
// Single-page login/register/forgot-password
// ----------------------------------------
Route::get('/', [UserController::class, 'showLoginForm'])->name('index');
Route::post('/login', [UserController::class, 'login'])->name('login.post');
Route::post('/register', [UserController::class, 'register'])->name('register.post');
Route::post('/verify-otp', [UserController::class, 'verifyOTP'])->name('verify.otp');
Route::post('/resend-otp', [UserController::class, 'resendOTP'])->name('resend.otp');
Route::post('/forgotpass', [UserController::class, 'forgotpassPost'])->name('forgotpass.post');


// ----------------------------------------
// Customer routes (Protected)
// ----------------------------------------
Route::middleware(['checkRole:customer,admin,rider'])->group(function () {
    Route::get('/customer/profile', [UserController::class, 'customerProfile'])->name('customer.profile');
    Route::post('/customer/profile', [UserController::class, 'updateCustomerProfile'])->name('customer.profile.update');
    Route::post('/customer/profile/password', [UserController::class, 'updateCustomerPassword'])->name('customer.profile.password');
    
    Route::get('/customer', [UserController::class, 'customerDashboard'])->name('customer.dashboard');
    Route::view('/customer/cart', 'customer.cart')->name('customer.cart');
    Route::view('/customer/orders', 'customer.orders')->name('customer.orders');

    // Customer API
    Route::get('/api/orders/my-orders', [OrderController::class, 'myOrders'])->name('api.orders.my');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout');
    Route::post('/api/orders/{id}/cancel', [OrderController::class, 'cancelOrder'])->name('api.orders.cancel');
});

// ----------------------------------------
// Rider routes (Protected)
// ----------------------------------------
Route::middleware(['checkRole:rider,admin'])->group(function () {
    Route::get('/rider/profile', [UserController::class, 'riderProfile'])->name('rider.profile');
    Route::post('/rider/profile', [UserController::class, 'updateRiderProfile'])->name('rider.profile.update');
    Route::post('/rider/profile/password', [UserController::class, 'updateRiderPassword'])->name('rider.profile.password');
    
    Route::get('/rider', [UserController::class, 'riderDashboard'])->name('rider.dashboard');
    Route::get('/rider/delivery', [UserController::class, 'riderDelivery'])->name('rider.delivery');

    // Rider API
    Route::get('/api/orders/available', [OrderController::class, 'availableOrders'])->name('api.orders.available');
    Route::post('/api/orders/{id}/accept', [OrderController::class, 'acceptOrder'])->name('api.orders.accept');
    Route::post('/api/orders/{id}/unaccept', [OrderController::class, 'unacceptOrder'])->name('api.orders.unaccept');
    Route::post('/api/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('api.orders.status');
    Route::post('/api/rider/status', [UserController::class, 'updateRiderStatus'])->name('api.rider.status.update');
});

// ----------------------------------------
// Logout
// ----------------------------------------
Route::get('/logout', [UserController::class, 'logout'])->name('logout');

// ----------------------------------------
// Admin routes (Protected)
// ----------------------------------------
Route::middleware(['checkRole:admin'])->group(function () {
    Route::get('/admin', [UserController::class, 'adminDashboard'])->name('admin.dashboard');
    Route::view('/admin/orders', 'admin.orders')->name('admin.orders');
    Route::view('/admin/modify-menu', 'admin.modify-menu')->name('admin.modify-menu');
    Route::get('/admin/manage-riders', [UserController::class, 'manageRidersPage'])->name('admin.manage-riders');
    Route::get('/admin/manage-customers', [UserController::class, 'manageCustomersPage'])->name('admin.manage-customers');
    Route::post('/admin/riders', [UserController::class, 'storeRider'])->name('admin.riders.store');
    Route::get('/admin/riders/list', [UserController::class, 'listRiders'])->name('admin.riders.list');
    Route::get('/admin/customers/list', [UserController::class, 'listCustomers'])->name('admin.customers.list');
    Route::delete('/admin/riders/{id}', [UserController::class, 'destroyRider'])->name('admin.riders.destroy');
    
    Route::get('/admin/messages', [\App\Http\Controllers\MessageController::class, 'index'])->name('admin.messages');
    Route::post('/admin/messages/{id}/read', [\App\Http\Controllers\MessageController::class, 'markAsRead'])->name('admin.messages.read');
    Route::delete('/admin/messages/{id}', [\App\Http\Controllers\MessageController::class, 'destroy'])->name('admin.messages.destroy');
    
    // Admin API
    Route::get('/api/admin/statistics', [OrderController::class, 'adminStatistics'])->name('api.admin.stats');
    Route::get('/api/admin/orders', [OrderController::class, 'allOrders'])->name('api.admin.orders');
    Route::post('/api/menu', [\App\Http\Controllers\MenuItemController::class, 'store'])->name('api.menu.store');
    Route::put('/api/menu/{id}', [\App\Http\Controllers\MenuItemController::class, 'update'])->name('api.menu.update');
    Route::delete('/api/menu/{id}', [\App\Http\Controllers\MenuItemController::class, 'destroy'])->name('api.menu.destroy');
    Route::put('/api/riders/{id}', [UserController::class, 'updateRider'])->name('api.riders.update');
});


// ----------------------------------------
// Instructor dashboard
// ----------------------------------------
Route::get('/instructor', [UserController::class, 'instructorDashboard'])->name('instructor.dashboard');

// ----------------------------------------
// Rider dashboards
// ----------------------------------------
Route::get('/rider', [UserController::class, 'riderDashboard'])->name('rider.dashboard');
Route::get('/rider/delivery', [UserController::class, 'riderDelivery'])->name('rider.delivery');

// ----------------------------------------
// Dashboard API (Real-time data)
// ----------------------------------------
Route::get('/total-riders', function () {
    return DB::table('user')
        ->where('role', 'rider')
        ->count();
});

// ----------------------------------------
// Redirects
// ----------------------------------------
Route::redirect('/customer/menu', '/customer')->name('customer.menu');
Route::redirect('/customer/my-orders', '/customer/orders')->name('customer.my-orders');

// ----------------------------------------
// Public API
// ----------------------------------------
Route::get('/api/menu', [\App\Http\Controllers\MenuItemController::class, 'index'])->name('api.menu.index');
Route::get('/api/most-sold', [\App\Http\Controllers\MenuItemController::class, 'mostSold'])->name('api.most-sold');

// ----------------------------------------
// Messaging
// ----------------------------------------
Route::post('/messages', [\App\Http\Controllers\MessageController::class, 'store'])->name('messages.store');

// ----------------------------------------
// Admin message management (Already moved to admin group)
// ----------------------------------------

// ----------------------------------------
// Password reset routes
// ----------------------------------------
Route::post('/verify-reset-otp', [UserController::class, 'verifyResetOTP'])->name('verify.reset.otp');
Route::post('/reset-password', [UserController::class, 'resetPassword'])->name('reset.password.post');
