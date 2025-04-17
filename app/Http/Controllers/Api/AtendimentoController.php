<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Atendimento;
use App\Models\Lead;
use App\Models\Cliente;
use Illuminate\Http\Request;

class AtendimentoController extends Controller
{
    public function getAtendimentosLead(Lead $lead)
    {
        try {
            $atendimentos = Atendimento::where('lead_id', $lead->id)
                ->with(['user', 'anexos'])
                ->orderBy('data_atendimento', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $atendimentos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar atendimentos do lead',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function historico(Cliente $cliente)
    {
        try {
            $atendimentos = $cliente->atendimentos()
                ->with(['user']) // Carrega o relacionamento com o usuário
                ->orderBy('data_atendimento', 'desc')
                ->get()
                ->map(function ($atendimento) {
                    return [
                        'id' => $atendimento->id,
                        'tipo_contato' => $atendimento->tipo_contato,
                        'descricao' => $atendimento->descricao,
                        'data_atendimento' => $atendimento->data_atendimento,
                        'status' => $atendimento->status,
                        'retorno' => $atendimento->retorno,
                        'data_retorno' => $atendimento->data_retorno,
                        'proxima_acao' => $atendimento->proxima_acao,
                        'vendedor' => $atendimento->user->name ?? 'Não atribuído'
                    ];
                });

            return response()->json($atendimentos);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao carregar os atendimentos'], 500);
        }
    }
}
