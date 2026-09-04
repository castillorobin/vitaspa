<?php
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

/*

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::resource('patients', PatientController::class);
    Route::resource('appointments', AppointmentController::class);
    
    //Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::middleware('auth')->group(function () {
    // Rutas requeridas por la barra de navegación de Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
});

});

*/


Route::middleware(['auth', 'verified'])->group(function () {

    // Rutas exclusivas para Administrador
    Route::middleware(['can:admin'])->group(function () {
        get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        get('/reports', [ReportController::class, 'index'])->name('reports.index');
        get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
        resource('users', UserController::class);
    });

    // Rutas accesibles para Recepción y Administrador
    resource('patients', PatientController::class);
    resource('appointments', AppointmentController::class);
});

require __DIR__.'/auth.php';
