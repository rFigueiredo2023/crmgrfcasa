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
use App\Http\Controllers\SegmentoController;
use App\Models\Cliente;
use App\Models\Atendimento;
// Importa os controllers dos Dashboards
use App\Http\Controllers\Dashboards\AdminDashboardController;
use App\Http\Controllers\Dashboards\VendasDashboardController;
use App\Http\Controllers\Dashboards\FinancialDashboardController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\LeadAtendimentoController;
use App\Http\Controllers\LeadAtendimentoFakeController;
use App\Http\Controllers\LeadHistoricoController;
use App\Http\Controllers\AssistenteController;

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
            Route::get('/segmentos', [SegmentoController::class, 'index'])->name('segmentos.index');
            Route::post('/segmentos', [SegmentoController::class, 'store'])->name('segmentos.store');
            Route::get('/segmentos/create', [SegmentoController::class, 'create'])->name('segmentos.create');
            Route::get('/segmentos/{segmento}/edit', [SegmentoController::class, 'edit'])->name('segmentos.edit');
            Route::put('/segmentos/{segmento}', [SegmentoController::class, 'update'])->name('segmentos.update');
            Route::delete('/segmentos/{segmento}', [SegmentoController::class, 'destroy'])->name('segmentos.destroy');
        });

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
            Route::get('/api/clientes/{cliente}/atendimentos', [ClienteController::class, 'atendimentos'])
                ->name('clientes.atendimentos');
            // Nova rota para o proxy de consulta de CNPJ
            Route::get('/api/consultar-cnpj/{cnpj}', [ClienteController::class, 'consultarCnpj'])->name('api.consultar-cnpj');

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

        // Rotas para atendimentos de leads
        Route::post('/leads/{lead}/atendimentos', [LeadAtendimentoController::class, 'store'])->name('lead.atendimentos.store');
        Route::get('/leads/{lead}/atendimentos', [LeadAtendimentoController::class, 'show'])->name('lead.atendimentos.show');
        Route::get('/atendimentos/{atendimento}/anexo', [LeadAtendimentoController::class, 'downloadAnexo'])->name('atendimento.anexo.download');

        // Rota de fallback para testar o problema com atendimento singular
        Route::post('/leads/{id}/atendimento', [LeadAtendimentoFakeController::class, 'store'])->name('lead.atendimento.store');

        // Rota de teste para diagnóstico
        Route::get('/teste-lead/{id}', function($id) {
            return response()->json([
                'success' => true,
                'teste' => true,
                'id' => $id
            ]);
        });

        // Rota alternativa para histórico de lead sem usar route model binding
        Route::get('/teste-historico-lead/{id}', function($id) {
            try {
                // Busca o lead diretamente pelo ID
                $lead = \App\Models\Lead::find($id);

                if (!$lead) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Lead não encontrado'
                    ], 404);
                }

                return response()->json([
                    'success' => true,
                    'lead_id' => $lead->id,
                    'razao_social' => $lead->razao_social,
                    'teste' => 'Rota de teste funcionando'
                ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ], 500);
            }
        });

        // Rotas para o assistente de desenvolvimento
        Route::get('/dev-assistente', [AssistenteController::class, 'index'])->name('dev-assistente');
        Route::post('/dev-assistente', [AssistenteController::class, 'perguntar'])->name('dev-assistente.perguntar');
    });
});

// Locale
Route::get('/lang/{locale}', [LanguageController::class, 'swap']);
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');

// Nova rota para o controlador especializado de históricos de leads
Route::get('/lead-historico/{id}', [LeadHistoricoController::class, 'index'])->name('lead.historico.index');
Route::post('/lead-historico/{id}', [LeadHistoricoController::class, 'store'])->name('lead.historico.store');

// Rota para o assistente de desenvolvimento
// Route::get('/dev-assistente', App\Http\Livewire\DevAssistant::class)->name('dev-assistente');

// API interna - Usuários
Route::get('/api/usuarios', function() {
    // Retornar lista de usuários com role vendedor/atendimento
    $usuarios = \App\Models\User::whereIn('role', ['admin', 'vendas'])
        ->select('id', 'name')
        ->orderBy('name')
        ->get();

    return response()->json($usuarios);
});
