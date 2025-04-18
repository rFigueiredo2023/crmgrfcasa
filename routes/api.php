<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ArquivoController;
use App\Http\Controllers\MensagemController;
use App\Http\Controllers\AtendimentoController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

// API para usuários (responsáveis)
Route::get('/usuarios', [UserController::class, 'index']);

// Rotas para clientes
Route::get('/clientes/{cliente}', [ClienteController::class, 'show']);
Route::post('/clientes', [ClienteController::class, 'store']);
Route::put('/clientes/{cliente}', [ClienteController::class, 'update']);

// Rotas para leads
Route::get('/leads/{lead}', [LeadController::class, 'show']);
Route::post('/leads', [LeadController::class, 'store']);
Route::post('/leads/com-atendimento', [LeadController::class, 'storeComAtendimento']);
Route::put('/leads/{lead}', [LeadController::class, 'update']);

// Rotas para atendimentos
Route::get('/atendimentos/{atendimento}', [AtendimentoController::class, 'show']);
Route::post('/atendimentos', [AtendimentoController::class, 'store']);
Route::put('/atendimentos/{atendimento}', [AtendimentoController::class, 'update']);
Route::post('/clientes/{cliente}/atendimentos', [AtendimentoController::class, 'storeClienteAtendimento']);
Route::post('/leads/{lead}/atendimentos', [AtendimentoController::class, 'storeLeadAtendimento']);

// Rotas para históricos (polimórficos)
Route::post('/leads/{lead}/historicos', [LeadController::class, 'storeHistorico']);
Route::post('/clientes/{cliente}/historicos', [ClienteController::class, 'storeHistorico']);

// Rotas para arquivos
Route::post('/clientes/{cliente}/arquivos', [ArquivoController::class, 'store']);
Route::delete('/arquivos/{arquivo}', [ArquivoController::class, 'destroy']);

// Rotas para mensagens
Route::post('/clientes/{cliente}/mensagens', [MensagemController::class, 'store']);
Route::patch('/mensagens/{mensagem}/lida', [MensagemController::class, 'marcarComoLida']);

// Rotas para histórico
Route::get('/clientes/{cliente}/atendimentos', [ClienteController::class, 'atendimentos']);
Route::get('/leads/{id}/atendimentos', [LeadController::class, 'historico']);
