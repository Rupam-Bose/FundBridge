<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;

use App\Http\Controllers\founder\DashboardController   as FounderDashboard;
use App\Http\Controllers\founder\VentureController     as FounderVentures;
use App\Http\Controllers\founder\CampaignController    as FounderCampaigns;
use App\Http\Controllers\founder\AnalyticsController   as FounderAnalytics;
use App\Http\Controllers\founder\InvestorActivityController as FounderInvestorActivity;
use App\Http\Controllers\founder\ProfileController     as FounderProfile;

use App\Http\Controllers\investor\DashboardController  as InvestorDashboard;
use App\Http\Controllers\investor\DiscoverController   as InvestorDiscover;
use App\Http\Controllers\investor\PortfolioController  as InvestorPortfolio;
use App\Http\Controllers\investor\ProfileController    as InvestorProfile;

use App\Http\Controllers\admin\DashboardController     as AdminDashboard;
use App\Http\Controllers\admin\UserController          as AdminUsers;
use App\Http\Controllers\admin\VentureController      as AdminVentures;
use App\Http\Controllers\admin\ReportController       as AdminReports;

use App\Http\Controllers\investor\CampaignController  as InvestorCampaigns;

use App\Http\Controllers\MessageController;
use App\Http\Controllers\VideoCallController;
use App\Http\Controllers\NotificationController;

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
Route::post('/login',    [LoginController::class,   'login'])->name('login.submit');
Route::post('/logout',   [LoginController::class,   'logout'])->name('logout');

/*
Forgot / Reset Password
*/

Route::get('/forgot-password',        [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password',       [ForgotPasswordController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password',        [ForgotPasswordController::class, 'resetPassword'])->name('password.update');

/*
Founder Routes
*/

Route::middleware(['auth', 'role:founder'])
    ->prefix('founder')
    ->name('founder.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [FounderDashboard::class, 'index'])->name('dashboard');

        // Ventures
        Route::get('/ventures',            [FounderVentures::class, 'index'])->name('ventures');
        Route::get('/ventures/create',     [FounderVentures::class, 'create'])->name('ventures.create');
        Route::post('/ventures',           [FounderVentures::class, 'store'])->name('ventures.store');
        Route::get('/ventures/{id}/edit',  [FounderVentures::class, 'edit'])->name('ventures.edit');
        Route::put('/ventures/{id}',       [FounderVentures::class, 'update'])->name('ventures.update');
        Route::delete('/ventures/{id}',    [FounderVentures::class, 'destroy'])->name('ventures.destroy');

        // Campaigns
        Route::get('/campaigns',           [FounderCampaigns::class, 'index'])->name('campaigns');
        Route::get('/campaigns/create',    [FounderCampaigns::class, 'create'])->name('campaigns.create');
        Route::post('/campaigns',          [FounderCampaigns::class, 'store'])->name('campaigns.store');
        Route::put('/campaigns/{id}',      [FounderCampaigns::class, 'update'])->name('campaigns.update');
        Route::delete('/campaigns/{id}',   [FounderCampaigns::class, 'destroy'])->name('campaigns.destroy');

        // Investor Activities
        Route::get('/investor-activities', [FounderInvestorActivity::class, 'index'])->name('investor-activities');

        // Analytics
        Route::get('/analytics',           [FounderAnalytics::class, 'index'])->name('analytics');

        // Profile
        Route::get('/profile',             [FounderProfile::class, 'index'])->name('profile');
        Route::put('/profile',             [FounderProfile::class, 'update'])->name('profile.update');
        Route::put('/profile/password',    [FounderProfile::class, 'updatePassword'])->name('profile.password');
    });

/*
Investor Routes
*/

Route::middleware(['auth', 'role:investor'])
    ->prefix('investor')
    ->name('investor.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [InvestorDashboard::class, 'index'])->name('dashboard');

        // Discover
        Route::get('/discover',                    [InvestorDiscover::class, 'index'])->name('discover');
        Route::post('/ventures/{id}/interest',     [InvestorDashboard::class, 'markInterest'])->name('interest');

        // Portfolio
        Route::get('/portfolio',                   [InvestorPortfolio::class, 'index'])->name('portfolio');
        Route::delete('/portfolio/{venture_id}',   [InvestorPortfolio::class, 'remove'])->name('portfolio.remove');
        Route::put('/portfolio/{venture_id}',      [InvestorPortfolio::class, 'updateInterest'])->name('portfolio.update');

        // Profile
        Route::get('/profile',                     [InvestorProfile::class, 'index'])->name('profile');
        Route::put('/profile',                     [InvestorProfile::class, 'update'])->name('profile.update');
        Route::put('/profile/password',            [InvestorProfile::class, 'updatePassword'])->name('profile.password');

        // Campaigns (from tracked ventures) + Invest
        Route::get('/campaigns',                   [InvestorCampaigns::class, 'index'])->name('campaigns');
        Route::post('/campaigns/{id}/invest',      [InvestorCampaigns::class, 'invest'])->name('campaigns.invest');
    });

/*
Admin Panel
*/

Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login', function () {
        return view('admin.login');
    })->name('login');

    Route::post('/login', [LoginController::class, 'adminLogin'])->name('login.submit');

    Route::middleware(['auth', 'role:admin'])->group(function () {
        Route::get('/dashboard',          [AdminDashboard::class, 'index'])->name('dashboard');
        Route::get('/users',              [AdminUsers::class, 'index'])->name('users');
        Route::post('/users/{id}/role',   [AdminUsers::class, 'toggleRole'])->name('users.role');
        Route::delete('/users/{id}',      [AdminUsers::class, 'destroy'])->name('users.destroy');

        // Ventures management
        Route::get('/ventures',           [AdminVentures::class, 'index'])->name('ventures');
        Route::put('/ventures/{id}/status', [AdminVentures::class, 'updateStatus'])->name('ventures.status');
        Route::delete('/ventures/{id}',   [AdminVentures::class, 'destroy'])->name('ventures.destroy');

        // Reports
        Route::get('/reports',            [AdminReports::class, 'index'])->name('reports');
    });
});

/*
API Routes (JSON) — for charts and AJAX
*/

Route::middleware('auth')->prefix('api')->name('api.')->group(function () {

    // Founder API
    Route::get('/founder/stats', function () {
        $user     = auth()->user();
        $ventures = $user->ventures;
        return response()->json([
            'total_raised'     => $ventures->sum('raised_amount'),
            'active_campaigns' => \App\Models\Campaign::whereIn('venture_id', $ventures->pluck('id'))->where('status', 'active')->count(),
            'total_views'      => $ventures->sum('views'),
            'unread_messages'  => $user->receivedMessages()->whereNull('read_at')->count(),
        ]);
    })->name('founder.stats');

    Route::get('/founder/analytics', [\App\Http\Controllers\founder\AnalyticsController::class, 'apiStats'])->name('founder.analytics');

    // Investor API
    Route::get('/ventures',   [\App\Http\Controllers\investor\DiscoverController::class, 'apiList'])->name('ventures');
    Route::get('/investor/investments', [InvestorCampaigns::class, 'apiMyInvestments'])->name('investor.investments');

    // Admin API
    Route::get('/admin/stats', [AdminReports::class, 'apiStats'])->name('admin.stats');

    // Notifications API
    Route::get('/notifications',          [NotificationController::class, 'list'])->name('notifications');
    Route::put('/notifications/{id}/read',[NotificationController::class, 'markRead'])->name('notifications.read');

    // Messages poll
    Route::get('/messages/{userId}/poll', [MessageController::class, 'poll'])->name('messages.poll');
});


/*
Messaging (Founder ↔ Investor)
*/

Route::middleware('auth')->group(function () {
    Route::get('/messages',             [MessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{userId}',    [MessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{userId}',   [MessageController::class, 'send'])->name('messages.send');
});

/* Video Call (Jitsi)
*/

Route::middleware('auth')->group(function () {
    Route::get('/call/{partnerId}', [VideoCallController::class, 'show'])->name('video.call');
});

/*
Notifications (mark all read)
*/

Route::middleware('auth')->group(function () {
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
});