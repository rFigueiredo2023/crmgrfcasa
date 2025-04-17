<?php

use App\Http\Controllers\ClienteController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ArquivoController;
use App\Http\Controllers\MensagemController;
use Illuminate\Support\Facades\Route;

// Rotas para arquivos
Route::post('/clientes/{cliente}/arquivos', [ArquivoController::class, 'store']);
Route::delete('/arquivos/{arquivo}', [ArquivoController::class, 'destroy']);

// Rotas para mensagens
Route::post('/clientes/{cliente}/mensagens', [MensagemController::class, 'store']);
Route::patch('/mensagens/{mensagem}/lida', [MensagemController::class, 'marcarComoLida']);

// Rotas para histórico
Route::get('/clientes/{cliente}/atendimentos', [ClienteController::class, 'atendimentos']);
Route::get('/leads/{id}/atendimentos', [LeadController::class, 'historico']);
