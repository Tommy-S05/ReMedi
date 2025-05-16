<?php

use App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function() {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function() {
    Route::get('dashboard', function() {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    // Rutas para Medicamentos
    Route::get('medications', [Controllers\MedicationController::class, 'index'])->name('medications.index');
    Route::get('medications/create', [Controllers\MedicationController::class, 'create'])->name('medications.create');
    Route::post('medications', [Controllers\MedicationController::class, 'store'])->name('medications.store');
    // Route::get('medications/{medication}', [Controllers\MedicationController::class, 'show'])->name('medications.show');
    Route::get('medications/{medication}/edit', [Controllers\MedicationController::class, 'edit'])->name('medications.edit');
    Route::put('medications/{medication}', [Controllers\MedicationController::class, 'update'])->name('medications.update');
    Route::delete('medications/{medication}', [Controllers\MedicationController::class, 'destroy'])->name('medications.destroy');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
