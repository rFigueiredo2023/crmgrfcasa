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
use App\Http\Controllers\AtendimentoController;
use App\Models\Cliente;
use App\Models\Atendimento;
// Importa os controllers dos Dashboards
use App\Http\Controllers\Dashboards\AdminDashboardController;
use App\Http\Controllers\Dashboards\VendasDashboardController;
use App\Http\Controllers\Dashboards\FinancialDashboardController;
use App\Http\Controllers\LeadController;

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
            Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])->name('clientes.edit');
            Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])->name('clientes.update');
            Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])->name('clientes.destroy');
            Route::get('/clientes/{cliente}/historico', [ClienteController::class, 'historico'])->name('clientes.historico');
            Route::post('/clientes/{cliente}/historico', [ClienteController::class, 'storeHistorico'])->name('clientes.historico.store');

            // Transportadoras
            Route::post('/transportadoras', [TransportadoraController::class, 'store'])->name('transportadoras.store');
            Route::get('/transportadoras', [TransportadoraController::class, 'index'])->name('transportadoras.index');

            // Veículos
            Route::post('/veiculos', [VeiculoController::class, 'store'])->name('veiculos.store');
            Route::get('/veiculos', [VeiculoController::class, 'index'])->name('veiculos.index');
        });

        // Atendimentos
        Route::prefix('atendimentos')->group(function () {
            Route::get('/', [AtendimentoController::class, 'index'])->name('atendimentos.index');
            Route::post('/', [AtendimentoController::class, 'store'])->name('atendimentos.store');
            Route::get('/search', [AtendimentoController::class, 'search'])->name('atendimentos.search');
            Route::post('/lead-com-atendimento', [AtendimentoController::class, 'storeLeadComAtendimento'])->name('atendimentos.store-lead');
        });

        Route::get('/atendimentos', [AtendimentoController::class, 'index'])->name('atendimentos.index');

        // Leads
        Route::prefix('leads')->group(function () {
            Route::get('/', [LeadController::class, 'index'])->name('leads.index');
            Route::post('/', [LeadController::class, 'store'])->name('leads.store');
            Route::put('/{lead}', [LeadController::class, 'update'])->name('leads.update');
            Route::delete('/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');
        });

        // Rotas de Leads
        Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
        Route::get('/leads/{lead}/historico', [LeadController::class, 'historico'])->name('leads.historico');
        Route::post('/leads/{lead}/historico', [LeadController::class, 'storeHistorico'])->name('leads.historico.store');
        Route::post('/leads/{lead}/converter', [LeadController::class, 'converter'])->name('leads.converter');
    });
});

// Locale
Route::get('/lang/{locale}', [LanguageController::class, 'swap']);
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');
