<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistroPontoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\SupervisorDashboardController;
use App\Http\Controllers\EstagiarioDashboardController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/registro-ponto', [RegistroPontoController::class, 'index'])->name('registro-ponto.index');
    Route::get('/gerar-relatoriomes', [RegistroPontoController::class, 'relatoriomes'])->name('gerarpdf.mes');

    Route::post('/registro-ponto/{tipo}', [RegistroPontoController::class, 'registrar'])->name('registro-ponto.registrar');

    // Rotas de admin
    Route::middleware(['can:viewAny,App\Models\RegistroPonto'])->group(function () {
        Route::get('/admin/registros-ponto', [RegistroPontoController::class, 'adminIndex'])->name('admin.registros-ponto');
        Route::get('/admin/relatorio-ponto', [RegistroPontoController::class, 'gerarRelatorio'])->name('admin.relatorio-ponto');
    });

    //rotas login 
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard')
        ->middleware('check.access.level:admin');

    Route::get('/supervisor/dashboard', [SupervisorDashboardController::class, 'index'])
        ->name('supervisor.dashboard')
        ->middleware('check.access.level:supervisor');

    Route::get('/estagiario/dashboard', [EstagiarioDashboardController::class, 'index'])
        ->name('estagiario.dashboard')
        ->middleware('check.access.level:estagiario');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
