<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SheepController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/sheep', [SheepController::class, 'index'])->name('sheep.index');

    Route::get('/sheep/create', [SheepController::class, 'create'])->name('sheep.create');
    Route::post('/sheep', [SheepController::class, 'store'])->name('sheep.store');
    Route::get('/sheep/{sheep}', [SheepController::class, 'show'])->name('sheep.show');
    Route::get('/sheep/{sheep}/edit', [SheepController::class, 'edit'])->name('sheep.edit');
    Route::put('/sheep/{sheep}', [SheepController::class, 'update'])->name('sheep.update');
});

require __DIR__.'/auth.php';
