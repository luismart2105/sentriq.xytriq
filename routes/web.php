<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\KitController as AdminKitController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReviewSubmissionController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/servicios', [ServiceController::class, 'index'])->name('services.index');
Route::get('/servicios/{service}', [ServiceController::class, 'show'])->name('services.show');

Route::view('/nosotros', 'about')->name('about');
Route::view('/garantias', 'warranty')->name('warranty');
Route::view('/contacto', 'contact')->name('contact');

Route::get('/opinar/{token}', [ReviewSubmissionController::class, 'show'])->name('reviews.show');
Route::post('/opinar/{token}', [ReviewSubmissionController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('reviews.store');

Route::prefix('admin')->name('admin.')->group(function (): void {
    Route::middleware('guest')->group(function (): void {
        Route::get('/ingresar', [AdminAuthController::class, 'create'])->name('login');
        Route::post('/ingresar', [AdminAuthController::class, 'store'])
            ->middleware('throttle:5,1')
            ->name('login.store');
    });

    Route::middleware('auth')->group(function (): void {
        Route::get('/', AdminDashboardController::class)->name('dashboard');
        Route::post('/salir', [AdminAuthController::class, 'destroy'])->name('logout');
        Route::get('/cuenta', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/cuenta', [AdminProfileController::class, 'update'])
            ->middleware('throttle:5,1')
            ->name('profile.update');
        Route::resource('kits', AdminKitController::class)->except('show');
        Route::get('/resenas', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::post('/resenas/invitaciones', [AdminReviewController::class, 'invite'])->name('reviews.invite');
        Route::patch('/resenas/{review}/estado', [AdminReviewController::class, 'status'])->name('reviews.status');
    });
});
