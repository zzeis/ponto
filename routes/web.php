<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegistroPontoController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\SupervisorDashboardController;
use App\Http\Controllers\EstagiarioDashboardController;
use App\Http\Controllers\UserController;
use App\Jobs\SendEmailJob;
use App\Models\RegistroPonto;


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




Route::middleware(['auth', 'user.active'])->group(function () {



    //rotas estagiarios
    Route::get('/registros/atualizar', [RegistroPontoController::class, 'atualizarRegistros'])
        ->name('registros.atualizar');
    Route::get('/registro-ponto', [RegistroPontoController::class, 'index'])->name('registro-ponto.index');


    Route::post('/registro-ponto/{tipo}', [RegistroPontoController::class, 'registrar'])->name('registro-ponto.registrar');

    Route::get('/gerar-relatoriomes', [RegistroPontoController::class, 'relatoriomes'])->name('gerarpdf.mes');


    // Rotas para supervisor e admin
    Route::middleware(['check.access.level:supervisor,admin'])->group(function () {


        Route::put('/registro-ponto/update-batch/{user}', [RegistroPontoController::class, 'updateBatch'])->name('registro-ponto.update-batch');
          
        Route::get('horarios/{user}', [AdminDashboardController::class, 'verifyHorarios'])->name('horarios.verificar');
        Route::get('listaEstagiarios', [AdminDashboardController::class, 'listUsersEstagiarios'])->name('listaEstagiarios');
        Route::post('registro-ponto/observacao/{data}', [RegistroPontoController::class, 'salvarObservacao'])->name('registro-ponto.observacao');
    });

    //registro ponto
    Route::get('horarios/{user}/download', [RegistroPontoController::class, 'downloadRegistrosByUser'])->name('registro-ponto.download');

    // Rotas de admin

    Route::middleware(['auth', 'check.access.level:admin'])->prefix('admin')->name('admin.')->group(function () {


        //registro ponto 
        Route::get('registros-ponto', [UserController::class, 'ListEstagiariosUsers'])->name('registros-ponto-all');

        Route::get('user/supervisores', [UserController::class, 'listSupervisoresUsers'])->name('user.supervisor');
        Route::get('relatorio-ponto', [UserController::class, 'gerarRelatorio'])->name('relatorio-ponto');


        //administração de usuarios
        Route::put('usuarios/{user}/status', [AdminDashboardController::class, 'toggleUserStatus'])->name('usuarios.status');
        Route::get('criarUsuario', [AdminDashboardController::class, 'createUserview'])->name('usuarios.criar');
        Route::post('saveUsuario', [AdminDashboardController::class, 'createUser'])->name('usuarios.save');
        Route::put('usuarios/{user}', [UserController::class, 'update'])->name('usuarios.update');
        Route::get('user/infomations/{user}', [UserController::class, 'viewUserInformations'])->name('user.informations');
    });



    //rotas login 
    Route::middleware(['auth', 'first.login'])->group(function () {
        Route::get('/estagiario/dashboard', [EstagiarioDashboardController::class, 'index'])
            ->name('estagiario.dashboard')
            ->middleware('check.access.level:estagiario');
        Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
            ->name('admin.dashboard')
            ->middleware('check.access.level:admin');

        Route::get('/supervisor/dashboard', [SupervisorDashboardController::class, 'index'])
            ->name('supervisor.dashboard')
            ->middleware('check.access.level:supervisor');
    });

    Route::middleware(['auth', 'first.login'])->group(function () {
        Route::get('/first-password-change', [UserController::class, 'showFirstPasswordChangeForm'])
            ->name('first.password.change');
        Route::post('/first-password-change', [UserController::class, 'updateFirstPassword'])
            ->name('first.password.update');
    });





    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
