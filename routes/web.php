<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\PartnerController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AgencyDashboardController;

Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/agences', [PublicController::class, 'agences'])->name('agences');
Route::get('/agences/{slug}', [PublicController::class, 'agence'])->name('agence.show');

Route::get('/devenir-partenaire', [PartnerController::class, 'showRegistrationForm'])->name('partner.register.form');
Route::post('/devenir-partenaire', [PartnerController::class, 'register'])->name('partner.register');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/agencies', [AdminDashboardController::class, 'agencies'])->name('agencies');
    Route::post('/agencies/{agency}/approve', [AdminDashboardController::class, 'approveAgency'])->name('agencies.approve');
    Route::post('/agencies/{agency}/reject', [AdminDashboardController::class, 'rejectAgency'])->name('agencies.reject');
    Route::post('/agencies/{agency}/suspend', [AdminDashboardController::class, 'suspendAgency'])->name('agencies.suspend');
    Route::post('/agencies/{agency}/reactivate', [AdminDashboardController::class, 'reactivateAgency'])->name('agencies.reactivate');
    Route::delete('/agencies/{agency}', [AdminDashboardController::class, 'deleteAgency'])->name('agencies.delete');
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users');
    Route::get('/flights', [AdminDashboardController::class, 'flights'])->name('flights');
    Route::get('/activity', [AdminDashboardController::class, 'activity'])->name('activity');
});

Route::middleware(['auth', 'role:admin_agence'])->prefix('admin-agence')->name('agency.')->group(function () {
    Route::get('/', [AgencyDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [AgencyDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [AgencyDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::get('/flights', [AgencyDashboardController::class, 'flights'])->name('flights');
    Route::get('/flights/create', [AgencyDashboardController::class, 'createFlight'])->name('flights.create');
    Route::post('/flights', [AgencyDashboardController::class, 'storeFlight'])->name('flights.store');
    Route::get('/flights/{flight}/edit', [AgencyDashboardController::class, 'editFlight'])->name('flights.edit');
    Route::put('/flights/{flight}', [AgencyDashboardController::class, 'updateFlight'])->name('flights.update');
    Route::delete('/flights/{flight}', [AgencyDashboardController::class, 'deleteFlight'])->name('flights.delete');
    Route::post('/flights/{flight}/duplicate', [AgencyDashboardController::class, 'duplicateFlight'])->name('flights.duplicate');
    Route::get('/routes', [AgencyDashboardController::class, 'routes'])->name('routes');
    Route::post('/routes', [AgencyDashboardController::class, 'updateRoutes'])->name('routes.update');
});

require __DIR__.'/auth.php';
