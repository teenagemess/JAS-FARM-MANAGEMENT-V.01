<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SheepController;
use App\Http\Controllers\PartnerController;
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

// Dashboard (Semua User)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// === GRUP RUTE ADMIN (Hanya untuk Aksi Delete dan Sensitif) ===
Route::middleware(['auth', 'role:admin'])->group(function () {

    // DELETE (Semua aksi penghapusan)
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::delete('sheep/{sheep}', [SheepController::class, 'destroy'])->name('sheep.destroy');
    Route::delete('shelters/{shelter}', [ShelterController::class, 'destroy'])->name('shelters.destroy');
    Route::delete('symptoms/{symptom}', [SymptomController::class, 'destroy'])->name('symptoms.destroy');
    Route::delete('feed-types/{feed_type}', [FeedTypeController::class, 'destroy'])->name('feed-types.destroy');
    Route::delete('feeding-records/{feeding_record}', [FeedingRecordController::class, 'destroy'])->name('feeding-records.destroy');
    Route::delete('profit-loss/{profit_loss}', [ProfitLossRecordController::class, 'destroy'])->name('profit-loss.destroy');

    // DELETE Timbangan, Kesehatan, Reproduksi (Resource Nested)
    Route::delete('weights/{weight}', [WeightRecordController::class, 'destroy'])->name('weights.destroy');
    Route::delete('health-records/{health_record}', [HealthRecordController::class, 'destroy'])->name('health-records.destroy');
    Route::delete('reproduction-records/{reproduction_record}', [ReproductionRecordController::class, 'destroy'])->name('reproduction-records.destroy');

    Route::resource('users', UserController::class);

    Route::get('/partners', [PartnerController::class, 'index'])->name('partners.index');
    Route::get('/partners/{partner}', [PartnerController::class, 'show'])->name('partners.show');
});

// === GRUP RUTE UTAMA (CRUD Non-Delete - Semua Boleh Akses) ===
Route::middleware('auth')->group(function () {

    // Profil (Semua User)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // 1. Domba
    Route::resource('sheep', SheepController::class)->except(['destroy']);
    Route::get('/sheep/{sheep}/print', [SheepController::class, 'printCard'])->name('sheep.print');

    // 2. Kandang
    Route::resource('shelters', ShelterController::class)->except(['show', 'destroy']);
    Route::get('/shelters/{shelter}/capacity', [ShelterController::class, 'getCapacity'])->name('shelters.capacity');

    // 3. Timbangan
    Route::resource('sheep.weights', WeightRecordController::class)
        ->shallow()
        ->only(['create', 'store']);

    // 4. Gejala (Master Data)
    Route::resource('symptoms', SymptomController::class)->except(['show', 'destroy']);

    // 5. Kesehatan
    Route::resource('sheep.health-records', HealthRecordController::class)
        ->shallow()
        ->only(['create', 'store', 'edit', 'update']);

    // 6. Reproduksi
    Route::resource('sheep.reproduction-records', ReproductionRecordController::class)
        ->shallow()
        ->only(['create', 'store', 'edit', 'update']);

    // 7. Jenis Pakan
    Route::resource('feed-types', FeedTypeController::class)->except(['show', 'destroy']);

    // 8. Pemberian Pakan
    Route::resource('feeding-records', FeedingRecordController::class)->except(['destroy']);

    // 9. Keuangan
    Route::resource('profit-loss', ProfitLossRecordController::class)->except(['show', 'edit', 'update', 'destroy']);

        // Rute untuk Request Penempatan (Hanya bisa diakses Mitra untuk ACC/Reject)
    Route::get('/placement-requests', [App\Http\Controllers\PlacementRequestController::class, 'index'])
        ->name('placement-requests.index');

    Route::post('/placement-requests/{id}/approve', [App\Http\Controllers\PlacementRequestController::class, 'approve'])
        ->name('placement-requests.approve');

    Route::post('/placement-requests/{id}/reject', [App\Http\Controllers\PlacementRequestController::class, 'reject'])
        ->name('placement-requests.reject');
});

require __DIR__ . '/auth.php';
