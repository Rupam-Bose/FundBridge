<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\founder\DashboardController as FounderDashboard;
use App\Http\Controllers\investor\DashboardController as InvestorDashboard;
use App\Http\Controllers\admin\DashboardController as AdminDashboard;
use App\Http\Controllers\admin\UserController as AdminUsers;


/*
Public Pages
*/

Route::get('/', function () {
    return view('fundbridge');
})->name('home');

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::get('/login', function () {
    return view('login');
})->name('login');


/*
Authentication Actions
*/

Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
Route::post('/login',    [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout',   [LoginController::class, 'logout'])->name('logout');


/*
Forgot / Reset Password
*/

Route::get('/forgot-password',         [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password',        [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}',  [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password',         [ForgotPasswordController::class, 'resetPassword'])->name('password.update');


/*
Founder Dashboard
*/

Route::middleware(['auth', 'role:founder'])->prefix('founder')->name('founder.')->group(function () {
    Route::get('/dashboard', [FounderDashboard::class, 'index'])->name('dashboard');
});


/*
Investor Dashboard
*/

Route::middleware(['auth', 'role:investor'])->prefix('investor')->name('investor.')->group(function () {
    Route::get('/dashboard',                       [InvestorDashboard::class, 'index'])->name('dashboard');
    Route::post('/ventures/{id}/interest',         [InvestorDashboard::class, 'markInterest'])->name('interest');
});


/*
 Admin Panel
*/

Route::prefix('admin')->name('admin.')->group(function () {

    // Admin login (no auth required)
    Route::get('/login', function () {
        return view('admin.login');
    })->name('login');

    Route::post('/login', [LoginController::class, 'adminLogin'])->name('login.submit');

    // Admin dashboard (requires auth + admin role)
    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/dashboard',            [AdminDashboard::class, 'index'])->name('dashboard');
        Route::get('/users',                [AdminUsers::class, 'index'])->name('users');
        Route::post('/users/{id}/role',     [AdminUsers::class, 'toggleRole'])->name('users.role');
        Route::delete('/users/{id}',        [AdminUsers::class, 'destroy'])->name('users.destroy');
    });

});


/*
API Routes (JSON) — for AJAX / chart data
*/

Route::middleware('auth')->prefix('api')->name('api.')->group(function () {

    Route::get('/founder/stats', function () {
        $user    = auth()->user();
        $ventures = $user->ventures;
        return response()->json([
            'total_raised'    => $ventures->sum('raised_amount'),
            'active_campaigns'=> \App\Models\Campaign::whereIn('venture_id', $ventures->pluck('id'))->where('status','active')->count(),
            'total_views'     => $ventures->sum('views'),
            'unread_messages' => $user->receivedMessages()->whereNull('read_at')->count(),
        ]);
    })->name('founder.stats');

    Route::get('/ventures', function () {
        return response()->json(
            \App\Models\Venture::where('status', 'active')->with('founder:id,name,company_name')->latest()->paginate(9)
        );
    })->name('ventures');

    Route::get('/admin/stats', function () {
        return response()->json([
            'total_users'    => \App\Models\User::count(),
            'total_ventures' => \App\Models\Venture::count(),
            'total_raised'   => \App\Models\Venture::sum('raised_amount'),
        ]);
    })->name('admin.stats');

});