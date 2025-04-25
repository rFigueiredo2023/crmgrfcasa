<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Lead;

class TestController extends Controller
{
    /**
     * Testa a API do CNPJa com um CNPJ específico.
     *
     * @param string $cnpj O CNPJ a ser consultado
     * @return \Illuminate\Http\JsonResponse
     */
    public function testeCnpja($cnpj)
    {
        if (!app()->environment('local', 'development')) {
            abort(404);
        }

        $cnpj = preg_replace('/\D/', '', $cnpj);

        if (strlen($cnpj) !== 14) {
            return response()->json(['error' => 'CNPJ inválido'], 400);
        }

        // Garante que o token não tem espaços extras
        $apiToken = trim(config('services.cnpja.token'));
        $baseUrl = config('services.cnpja.base_url', 'https://api.cnpja.com');

        // Log do token para debug
        \Log::info('Token CNPJa usado na chamada de teste:', [
            'token' => $apiToken,
            'token_length' => strlen($apiToken)
        ]);

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiToken
            ])->get("{$baseUrl}/office/{$cnpj}?registrations=BR&suframa=true");

            return response()->json([
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->json(),
                'token_used' => $apiToken,
                'token_length' => strlen($apiToken),
                'url' => "{$baseUrl}/office/{$cnpj}?registrations=BR&suframa=true"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'token_used' => $apiToken,
                'url' => "{$baseUrl}/office/{$cnpj}?registrations=BR&suframa=true"
            ], 500);
        }
    }

    /**
     * Testa a consulta de lead por ID.
     *
     * @param int $id O ID do lead
     * @return \Illuminate\Http\JsonResponse
     */
    public function testeLead($id)
    {
        return response()->json([
            'success' => true,
            'teste' => true,
            'id' => $id
        ]);
    }

    /**
     * Teste alternativo para histórico de lead sem usar route model binding.
     *
     * @param int $id O ID do lead
     * @return \Illuminate\Http\JsonResponse
     */
    public function testeHistoricoLead($id)
    {
        try {
            // Busca o lead diretamente pelo ID
            $lead = Lead::find($id);

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
    }
}
