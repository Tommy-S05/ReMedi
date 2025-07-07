<?php

declare(strict_types=1);

use App\Http\Controllers;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
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

    // Rutas para Prescripciones
    Route::resource('prescriptions', Controllers\PrescriptionController::class);
    Route::get('/prescriptions/{prescription}/export-pdf', [Controllers\PrescriptionController::class, 'exportPdf'])
        ->name('prescriptions.export.pdf');

    // --- Ruta para el Historial de Tomas ---
    Route::get('/history', [Controllers\HistoryController::class, 'index'])->name('history.index');

    // Rutas Api con la autenticación de Web
    Route::prefix('api')->group(function () {
        // Ruta para registrar la toma de un medicamento
        Route::post('/medication-logs', [Controllers\MedicationLogController::class, 'store'])->name('medication-logs.store');

        // --- Rutas para Notificaciones ---
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [Controllers\NotificationController::class, 'index'])->name('index');
            Route::post('/{notification}/read', [Controllers\NotificationController::class, 'markAsRead'])->name('markAsRead');
            Route::post('/mark-all-as-read', [Controllers\NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
        });

        Route::get('/history', [Controllers\HistoryController::class, 'fetch'])->name('history.fetch');
    });
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
