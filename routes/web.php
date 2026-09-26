<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\KitController as AdminKitController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\ProspectActivityController as AdminProspectActivityController;
use App\Http\Controllers\Admin\ProspectController as AdminProspectController;
use App\Http\Controllers\Admin\QuoteController as AdminQuoteController;
use App\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Http\Controllers\ClientQuoteSignatureController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ReviewSubmissionController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WhatsappClickController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/robots.txt', [SeoController::class, 'robots'])->name('robots');
Route::get('/sitemap.xml', [SeoController::class, 'sitemap'])->name('sitemap');

Route::get('/servicios', [ServiceController::class, 'index'])->name('services.index');
Route::get('/servicios/{service}', [ServiceController::class, 'show'])->name('services.show');

Route::view('/nosotros', 'about')->name('about');
Route::view('/garantias', 'warranty')->name('warranty');
Route::get('/contacto', [ContactController::class, 'create'])->name('contact');
Route::post('/contacto', [ContactController::class, 'store'])->middleware('throttle:5,1')->name('contact.store');
Route::view('/aviso-de-privacidad', 'privacy')->name('privacy');
Route::post('/eventos/whatsapp', [WhatsappClickController::class, 'store'])
    ->middleware('throttle:60,1')->name('events.whatsapp');

Route::get('/opinar/{token}', [ReviewSubmissionController::class, 'show'])->name('reviews.show');
Route::post('/opinar/{token}', [ReviewSubmissionController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('reviews.store');

Route::get('/presupuesto/{token}/firmar', [ClientQuoteSignatureController::class, 'show'])->name('quotes.sign');
Route::get('/presupuesto/{token}', [ClientQuoteSignatureController::class, 'document'])->name('quotes.document');
Route::post('/presupuesto/{token}/firmar', [ClientQuoteSignatureController::class, 'store'])
    ->middleware('throttle:10,1')
    ->name('quotes.sign.store');

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
        Route::get('/prospectos/hoy', [AdminProspectController::class, 'today'])->name('prospects.today');
        Route::post('/prospectos/{prospect}/actividades', [AdminProspectActivityController::class, 'store'])->name('prospects.activities.store');
        Route::resource('prospectos', AdminProspectController::class)
            ->parameters(['prospectos' => 'prospect'])
            ->except('destroy')
            ->names('prospects');
        Route::post('/presupuestos/{quote}/duplicar', [AdminQuoteController::class, 'duplicate'])->name('quotes.duplicate');
        Route::post('/presupuestos/{quote}/enlace-firma', [AdminQuoteController::class, 'signingLink'])->name('quotes.signing-link');
        Route::resource('presupuestos', AdminQuoteController::class)
            ->parameters(['presupuestos' => 'quote'])
            ->names('quotes');
        Route::get('/resenas', [AdminReviewController::class, 'index'])->name('reviews.index');
        Route::post('/resenas/invitaciones', [AdminReviewController::class, 'invite'])->name('reviews.invite');
        Route::patch('/resenas/{review}/estado', [AdminReviewController::class, 'status'])->name('reviews.status');
    });
});
