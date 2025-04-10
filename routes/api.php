Route::middleware('auth')->group(function () {
    // Rotas para arquivos
    Route::post('/clientes/{cliente}/arquivos', [ArquivoController::class, 'store']);
    Route::delete('/arquivos/{arquivo}', [ArquivoController::class, 'destroy']);

    // Rotas para mensagens
    Route::post('/clientes/{cliente}/mensagens', [MensagemController::class, 'store']);
    Route::patch('/mensagens/{mensagem}/lida', [MensagemController::class, 'marcarComoLida']);

    // Rota para detalhes do cliente
    Route::get('/clientes/{cliente}/detalhes', [ClienteController::class, 'detalhes']);
});