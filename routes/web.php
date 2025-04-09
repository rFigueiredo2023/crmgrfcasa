<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\language\LanguageController;
use App\Http\Controllers\pages\HomePage;
use App\Http\Controllers\pages\Page2;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\pages\Custormers;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\TransportadoraController;
use App\Http\Controllers\VeiculoController;
// Importa os controllers dos Dashboards
use App\Http\Controllers\Dashboards\AdminDashboardController;
use App\Http\Controllers\Dashboards\VendasDashboardController;
use App\Http\Controllers\Dashboards\FinancialDashboardController;

// Rotas públicas
Route::middleware('web')->group(function () {
    // Autenticação
    Route::get('/login', [LoginBasic::class, 'index'])->name('login')->middleware('guest');
    Route::post('/login', [LoginBasic::class, 'login'])->name('login.submit');
    Route::post('/logout', [LoginBasic::class, 'logout'])->name('logout');

    // Rotas protegidas
    Route::middleware('auth')->group(function () {
        // Rota raiz com redirecionamento
        Route::get('/', function () {
            $user = auth()->user();
            switch ($user->role) {
                case 'admin':
                    return redirect()->route('dashboard.admin');
                case 'vendas':
                    return redirect()->route('dashboard.vendas');
                case 'financeiro':
                    return redirect()->route('dashboard.financeiro');
                default:
                    return redirect()->route('login');
            }
        });

        // Dashboards
        Route::get('/admin', [AdminDashboardController::class, 'index'])->name('dashboard.admin');
        Route::get('/vendas', [VendasDashboardController::class, 'index'])->name('dashboard.vendas');
        Route::get('/financeiro', [FinancialDashboardController::class, 'index'])->name('dashboard.financeiro');

        // Customers
        Route::prefix('customers')->group(function () {
            Route::get('/', [Custormers::class, 'index'])->name('pages-customers');
            
            // Clientes
            Route::post('/clientes', [ClienteController::class, 'store'])->name('clientes.store');
            Route::get('/clientes', [ClienteController::class, 'index'])->name('clientes.index');

            // Transportadoras
            Route::post('/transportadoras', [TransportadoraController::class, 'store'])->name('transportadoras.store');
            Route::get('/transportadoras', [TransportadoraController::class, 'index'])->name('transportadoras.index');

            // Veículos
            Route::post('/veiculos', [VeiculoController::class, 'store'])->name('veiculos.store');
            Route::get('/veiculos', [VeiculoController::class, 'index'])->name('veiculos.index');
        });
    });
});

// Locale
Route::get('/lang/{locale}', [LanguageController::class, 'swap']);
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');
