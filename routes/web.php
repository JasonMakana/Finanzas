<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\CuentaController;
use App\Http\Controllers\DeudaController;
use App\Http\Controllers\TransferenciaController;
use App\Http\Controllers\ProyeccionController;

Route::get('/', function () {
    return view('welcome');
});

// Ruta hacia le Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Rutas para el manejo de movimientos
Route::get('/movimientos/crear', [MovimientoController::class, 'create'])->name('movimientos.create');
Route::post('/movimientos', [MovimientoController::class, 'store'])->name('movimientos.store');

// Rutas para el manejo de categorías
Route::get('/categorias/crear', [CategoriaController::class, 'create'])->name('categorias.create');
Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');

// Ruta para la vista de cuentas
Route::get('/cuentas', [CuentaController::class, 'index'])->name('cuentas.index');
Route::get('/cuentas/crear', [\App\Http\Controllers\CuentaController::class, 'create'])->name('cuentas.create');
Route::get('/cuentas/{cuenta}/editar', [\App\Http\Controllers\CuentaController::class, 'edit'])->name('cuentas.edit');

Route::post('/cuentas', [\App\Http\Controllers\CuentaController::class, 'store'])->name('cuentas.store'); // 
Route::put('/cuentas/{cuenta}', [\App\Http\Controllers\CuentaController::class, 'update'])->name('cuentas.update');

// Ruta para ocultar una deuda
Route::patch('/deudas/{deuda}/ocultar', [DeudaController::class, 'ocultar'])->name('deudas.ocultar');
Route::patch('/deudas/{deuda}/mostrar', [DeudaController::class, 'mostrar'])->name('deudas.mostrar');

// Rutas para transferencias
Route::get('/transferencias/crear', [TransferenciaController::class, 'create'])->name('transferencias.create');
Route::post('/transferencias', [TransferenciaController::class, 'store'])->name('transferencias.store');

// Rutas para editar y actualizar movimientos
Route::get('/movimientos/{movimiento}/editar', [App\Http\Controllers\MovimientoController::class, 'edit'])->name('movimientos.edit');
Route::put('/movimientos/{movimiento}', [App\Http\Controllers\MovimientoController::class, 'update'])->name('movimientos.update');
Route::delete('/movimientos/{movimiento}', [App\Http\Controllers\MovimientoController::class, 'destroy'])->name('movimientos.destroy');

// Rutas de proyecciones
Route::get('/planeacion', [ProyeccionController::class, 'index'])->name('proyecciones.index');
Route::post('/planeacion/ingreso', [ProyeccionController::class, 'updateIngreso'])->name('proyecciones.ingreso');

// Rutas para las deudas
Route::get('/deudas/crear', [DeudaController::class, 'create'])->name('deudas.create');
Route::post('/deudas', [DeudaController::class, 'store'])->name('deudas.store');

// Rutas para gastos fijos
Route::get('/gastos-fijos/crear', [\App\Http\Controllers\GastoFijoController::class, 'create'])->name('gastos-fijos.create');
Route::post('/gastos-fijos', [\App\Http\Controllers\GastoFijoController::class, 'store'])->name('gastos-fijos.store');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
