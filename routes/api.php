use App\Http\Controllers\AtendimentoController;
use App\Http\Controllers\ArquivoController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\MensagemController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function () {
    // Rotas para arquivos
    Route::post('/clientes/{cliente}/arquivos', [ArquivoController::class, 'store']);
    Route::delete('/arquivos/{arquivo}', [ArquivoController::class, 'destroy']);

    // Rotas para mensagens
    Route::post('/clientes/{cliente}/mensagens', [MensagemController::class, 'store']);
    Route::patch('/mensagens/{mensagem}/lida', [MensagemController::class, 'marcarComoLida']);

    // Rota para detalhes do cliente
    Route::get('/clientes/{cliente}/detalhes', [ClienteController::class, 'detalhes']);

    // Rota para obter atendimentos de um cliente
    Route::get('/clientes/{cliente}/atendimentos', [AtendimentoController::class, 'getByCliente'])
        ->name('api.clientes.atendimentos');
});

Route::middleware(['api', 'auth'])->group(function () {
    Route::get('/clientes/{cliente}/atendimentos', [AtendimentoController::class, 'getByCliente'])
        ->name('api.clientes.atendimentos');
});