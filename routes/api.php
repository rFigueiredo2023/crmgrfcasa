<?php

use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ArquivoController;
use App\Http\Controllers\AtendimentoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\MensagemController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aqui são registradas as rotas da API do sistema CRM.
| Estas rotas são carregadas pelo RouteServiceProvider e todas recebem
| automaticamente o prefixo "api" e o grupo de middleware "api".
|
*/

// API v1
Route::prefix('v1')->group(function () {
    /**
     * Usuários
     * Endpoints para gerenciamento de usuários do sistema
     */
    Route::get('/usuarios', [UserController::class, 'index']);

    /**
     * Clientes
     * Endpoints para gerenciamento de clientes
     */
    Route::apiResource('clientes', ClienteController::class)->only(['show', 'store', 'update']);
    Route::get('/clientes/{cliente}/atendimentos', [ClienteController::class, 'atendimentos']);
    Route::post('/clientes/{cliente}/historicos', [ClienteController::class, 'storeHistorico']);
    Route::post('/clientes/{cliente}/atendimentos', [AtendimentoController::class, 'storeClienteAtendimento']);
    Route::post('/clientes/{cliente}/arquivos', [ArquivoController::class, 'store']);
    Route::post('/clientes/{cliente}/mensagens', [MensagemController::class, 'store']);

    /**
     * Leads
     * Endpoints para gerenciamento de leads (potenciais clientes)
     */
    Route::apiResource('leads', LeadController::class)->only(['show', 'store', 'update']);
    Route::post('/leads/com-atendimento', [LeadController::class, 'storeComAtendimento']);
    Route::post('/leads/{lead}/historicos', [LeadController::class, 'storeHistorico']);
    Route::post('/leads/{lead}/atendimentos', [AtendimentoController::class, 'storeLeadAtendimento']);
    Route::get('/leads/{id}/atendimentos', [LeadController::class, 'historico']);

    /**
     * Atendimentos
     * Endpoints para gerenciamento de atendimentos
     */
    Route::apiResource('atendimentos', AtendimentoController::class)->only(['show', 'store', 'update']);

    /**
     * Arquivos
     * Endpoints para gerenciamento de arquivos
     */
    Route::delete('/arquivos/{arquivo}', [ArquivoController::class, 'destroy']);

    /**
     * Mensagens
     * Endpoints para gerenciamento de mensagens
     */
    Route::patch('/mensagens/{mensagem}/lida', [MensagemController::class, 'marcarComoLida']);
});
