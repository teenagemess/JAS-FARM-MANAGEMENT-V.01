<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SheepController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShelterController;
use App\Http\Controllers\SymptomController;
use App\Http\Controllers\FeedTypeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HealthRecordController;
use App\Http\Controllers\WeightRecordController;
use App\Http\Controllers\FeedingRecordController;
use App\Http\Controllers\ProfitLossRecordController;
use App\Http\Controllers\ReproductionRecordController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    //Sheep
    Route::resource('sheep', SheepController::class);

    //Shelter
    Route::resource('shelters', ShelterController::class)->except(['show']);
    Route::get('/shelters/{shelter}/capacity', [ShelterController::class, 'getCapacity'])->name('shelters.capacity');

    //Weight Records
    Route::resource('sheep.weights', WeightRecordController::class)
    ->shallow()
    ->only(['create', 'store', 'destroy']);

    //Symptoms
    Route::resource('symptoms', SymptomController::class)->except(['show']);

    //Health Records
    Route::resource('sheep.health-records', HealthRecordController::class)
    ->shallow()
    ->only(['create', 'store', 'destroy']);

    //Reproduction Records
    Route::resource('sheep.reproduction-records', ReproductionRecordController::class)
        ->shallow()
        ->only(['create', 'store', 'destroy', 'edit', 'update']);

    Route::resource('feed-types', FeedTypeController::class)->except(['show']);

    Route::resource('feeding-records', FeedingRecordController::class);

    Route::resource('profit-loss', ProfitLossRecordController::class)->except(['show', 'edit', 'update']);
});

require __DIR__.'/auth.php';
