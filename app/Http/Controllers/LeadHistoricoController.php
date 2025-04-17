<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Historico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LeadHistoricoController extends Controller
{
    /**
     * Retorna os históricos de um lead específico pelo ID
     */
    public function index($id)
    {
        // Registra chamada para diagnóstico
        Log::info("LeadHistoricoController@index chamado para lead ID: $id");

        try {
            // Busca o lead
            $lead = Lead::findOrFail($id);

            // Registra que lead foi encontrado
            Log::info("Lead encontrado:", ['lead_id' => $lead->id, 'razao_social' => $lead->razao_social]);

            // Busca os históricos com relacionamento de usuário
            $historicos = $lead->historicos()->with('user')->latest('data')->get();

            // Registra quantos históricos foram encontrados
            Log::info("Históricos encontrados: " . $historicos->count());

            // Retorna resposta simples
            return response()->json([
                'success' => true,
                'data' => $historicos
            ]);

        } catch (\Exception $e) {
            // Registra erro completo
            Log::error("Erro ao buscar históricos do lead ID $id: " . $e->getMessage());
            Log::error($e->getTraceAsString());

            // Retorna mensagem de erro clara
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Não foi possível recuperar os históricos do lead.'
            ], 500);
        }
    }

    /**
     * Adiciona um novo histórico a um lead
     */
    public function store(Request $request, $id)
    {
        // Registra chamada para diagnóstico
        Log::info("LeadHistoricoController@store chamado para lead ID: $id", ['request' => $request->all()]);

        try {
            // Validação básica
            $validated = $request->validate([
                'tipo' => 'required|string',
                'texto' => 'required|string',
                'data' => 'nullable|date',
                'proxima_acao' => 'nullable|string',
                'data_proxima_acao' => 'nullable|date',
                'retorno' => 'nullable|string',
                'data_retorno' => 'nullable|date'
            ]);

            // Busca o lead
            $lead = Lead::findOrFail($id);

            // Cria o histórico usando o relacionamento polimórfico
            $historico = $lead->historicos()->create([
                'user_id' => auth()->id(),
                'data' => $validated['data'] ?? now(),
                'tipo' => $validated['tipo'],
                'texto' => $validated['texto'],
                'proxima_acao' => $validated['proxima_acao'] ?? null,
                'data_proxima_acao' => $validated['data_proxima_acao'] ?? null,
                'retorno' => $validated['retorno'] ?? null,
                'data_retorno' => $validated['data_retorno'] ?? null
            ]);

            // Carrega relacionamento do usuário
            $historico->load('user');

            // Retorna o histórico criado
            return response()->json([
                'success' => true,
                'message' => 'Histórico registrado com sucesso',
                'data' => $historico
            ]);

        } catch (\Exception $e) {
            // Registra erro
            Log::error("Erro ao salvar histórico para lead ID $id: " . $e->getMessage());
            Log::error($e->getTraceAsString());

            // Retorna erro
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Não foi possível registrar o histórico.'
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
