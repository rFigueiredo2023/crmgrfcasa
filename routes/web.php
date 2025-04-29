<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
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
use App\Http\Controllers\SegmentoController;
use App\Models\Cliente;
use App\Models\Atendimento;
// Importa os controllers dos Dashboards
use App\Http\Controllers\Dashboards\AdminDashboardController;
use App\Http\Controllers\Dashboards\VendasDashboardController;
use App\Http\Controllers\Dashboards\FinancialDashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadAtendimentoController;
use App\Http\Controllers\LeadHistoricoController;
use App\Http\Controllers\AssistenteController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\UserController;

// Locale
Route::get('/lang/{locale}', [LanguageController::class, 'swap']);
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages.misc-error');

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
        Route::get('/admin', [AdminDashboardController::class, 'index'])
            ->name('dashboard.admin')
            ->middleware('auth');

        Route::get('/vendas', [VendasDashboardController::class, 'index'])
            ->name('dashboard.vendas')
            ->middleware('vendas');

        Route::get('/financeiro', [FinancialDashboardController::class, 'index'])
            ->name('dashboard.financeiro')
            ->middleware('financeiro');

        // Segmentos (protegidos pelo middleware admin)
        Route::middleware('admin')->group(function () {
            Route::resource('segmentos', SegmentoController::class);
        });

        // Customers
        Route::prefix('customers')->group(function () {
            Route::get('/', [Custormers::class, 'index'])->name('customers.index');

            // Clientes
            Route::resource('clientes', ClienteController::class)->except(['show']);
            Route::get('/clientes/{cliente}/historico', [ClienteController::class, 'historico'])->name('clientes.historico');
            Route::post('/clientes/{cliente}/historico', [ClienteController::class, 'storeHistorico'])->name('clientes.historico.store');
            // Rota para os atendimentos de um cliente (para a UI do sistema)
            Route::get('/api/clientes/{cliente}/atendimentos', [ClienteController::class, 'atendimentos'])
                ->name('clientes.atendimentos');
            // Consulta de CNPJ
            Route::get('/api/consultar-cnpj/{cnpj}', [ClienteController::class, 'consultarCnpj'])->name('api.consultar-cnpj');

            // Transportadoras
            Route::resource('transportadoras', TransportadoraController::class)->only(['index', 'store']);

            // Veículos
            Route::resource('veiculos', VeiculoController::class)->only(['index', 'store']);
        });

        // Rota para histórico completo (para o modal de histórico)
        Route::get('/historico/cliente/{cliente}', [AtendimentoController::class, 'historico'])
            ->name('historico.cliente');

        // Atendimentos
        Route::prefix('atendimentos')->group(function () {
            Route::get('/', [AtendimentoController::class, 'index'])->name('atendimentos.index');
            Route::post('/', [AtendimentoController::class, 'store'])->name('atendimentos.store');
            Route::get('/search', [AtendimentoController::class, 'search'])->name('atendimentos.search');
            Route::post('/lead-com-atendimento', [AtendimentoController::class, 'storeLeadComAtendimento'])->name('atendimentos.store-lead');
        });

        // Leads
        Route::resource('leads', LeadController::class)->except(['show', 'edit', 'create']);
        Route::get('/leads/{lead}/historico', [LeadController::class, 'historico'])->name('leads.historico');
        Route::post('/leads/{lead}/historico', [LeadController::class, 'storeHistorico'])->name('leads.historico.store');
        Route::post('/leads/{lead}/converter', [LeadController::class, 'converter'])->name('leads.converter');

        // Atendimentos de leads
        Route::post('/leads/{lead}/atendimentos', [LeadAtendimentoController::class, 'store'])->name('lead.atendimentos.store');
        Route::get('/leads/{lead}/atendimentos', [LeadAtendimentoController::class, 'show'])->name('lead.atendimentos.show');
        Route::get('/atendimentos/{atendimento}/anexo', [LeadAtendimentoController::class, 'downloadAnexo'])->name('atendimento.anexo.download');

        // Histórico de leads
        Route::get('/lead-historico/{id}', [LeadHistoricoController::class, 'index'])->name('lead.historico.index');
        Route::post('/lead-historico/{id}', [LeadHistoricoController::class, 'store'])->name('lead.historico.store');

        // Assistente de desenvolvimento
        Route::get('/dev-assistente', [AssistenteController::class, 'index'])->name('dev-assistente');
        Route::post('/dev-assistente', [AssistenteController::class, 'perguntar'])->name('dev-assistente.perguntar');

        // Rota global para consulta de CNPJ
        Route::get('/consultar-cnpj/{cnpj}', [ClienteController::class, 'consultarCnpj'])->name('global.consultar-cnpj');

        // API interna - Usuários
        Route::get('/api/usuarios', [UserController::class, 'listarUsuariosVendas'])->name('api.usuarios');

        // Rotas para ambiente de desenvolvimento
        if (app()->environment('local', 'development')) {
            Route::prefix('teste')->group(function () {
                Route::get('/teste-cnpja/{cnpj}', [TestController::class, 'testeCnpja']);
                Route::get('/teste-lead/{id}', [TestController::class, 'testeLead']);
                Route::get('/teste-historico-lead/{id}', [TestController::class, 'testeHistoricoLead']);
            });
        }
    });
});
