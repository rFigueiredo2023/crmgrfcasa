<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Historico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class LeadAtendimentoController extends Controller
{
    public function store(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'tipo' => 'required|string|in:Ligação,WhatsApp,E-mail,Visita,Reunião,Outro',
            'descricao' => 'required|string',
            'proxima_acao' => 'nullable|string',
            'data_proxima_acao' => 'nullable|date|after_or_equal:today',
            'retorno' => 'nullable|string',
            'data_retorno' => 'nullable|date|after_or_equal:today',
            'ativar_lembrete' => 'nullable|boolean',
            'anexo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:5120'
        ]);

        try {
            DB::beginTransaction();

            $anexoPath = null;
            if ($request->hasFile('anexo')) {
                $file = $request->file('anexo');
                $anexoPath = $file->store('historicos/anexos', 'public');
            }

            $historico = $lead->historicos()->create([
                'user_id' => auth()->id(),
                'data' => now(),
                'tipo' => $validated['tipo'],
                'texto' => $validated['descricao'],
                'proxima_acao' => $validated['proxima_acao'],
                'data_proxima_acao' => $validated['data_proxima_acao'],
                'retorno' => $validated['retorno'],
                'data_retorno' => $validated['data_retorno'],
                'ativar_lembrete' => $request->boolean('ativar_lembrete'),
                'anexo' => $anexoPath
            ]);

            // Atualiza os dados do lead
            $lead->update([
                'data_proxima_acao' => $validated['data_proxima_acao'],
                'data_retorno' => $validated['data_retorno'],
                'ativar_lembrete' => $request->boolean('ativar_lembrete')
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Atendimento registrado com sucesso!',
                'historico' => [
                    'data' => $historico->data->format('d/m/Y H:i'),
                    'vendedora' => auth()->user()->name,
                    'tipo' => $historico->tipo,
                    'texto' => $historico->texto,
                    'proxima_acao' => $historico->proxima_acao,
                    'data_proxima_acao' => $historico->data_proxima_acao ? $historico->data_proxima_acao->format('d/m/Y') : null,
                    'retorno' => $historico->retorno,
                    'data_retorno' => $historico->data_retorno ? $historico->data_retorno->format('d/m/Y') : null,
                    'ativar_lembrete' => $historico->ativar_lembrete,
                    'anexo' => $historico->anexo ? Storage::url($historico->anexo) : null
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar atendimento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Lead $lead)
    {
        $historicos = $lead->historicos()
            ->with('usuario')
            ->orderBy('data', 'desc')
            ->get()
            ->map(function($historico) {
                return [
                    'data' => $historico->data->format('d/m/Y H:i'),
                    'vendedora' => $historico->usuario->name,
                    'tipo' => $historico->tipo,
                    'texto' => $historico->texto,
                    'proxima_acao' => $historico->proxima_acao,
                    'data_proxima_acao' => $historico->data_proxima_acao ? $historico->data_proxima_acao->format('d/m/Y') : null,
                    'retorno' => $historico->retorno,
                    'data_retorno' => $historico->data_retorno ? $historico->data_retorno->format('d/m/Y') : null,
                    'ativar_lembrete' => $historico->ativar_lembrete,
                    'anexo' => $historico->anexo ? Storage::url($historico->anexo) : null
                ];
            });

        return response()->json([
            'success' => true,
            'lead' => [
                'nome_empresa' => $lead->nome_empresa,
                'cnpj' => $lead->cnpj ?? 'Não informado',
                'telefone' => $lead->telefone,
                'contato' => $lead->contato,
                'endereco' => $lead->endereco ?? 'Não informado',
                'vendedora' => $lead->vendedor->name ?? 'Não atribuído'
            ],
            'historicos' => $historicos
        ]);
    }

    public function downloadAnexo(Historico $atendimento)
    {
        if (!$atendimento->anexo) {
            abort(404, 'Anexo não encontrado');
        }

        if (!Storage::disk('public')->exists($atendimento->anexo)) {
            abort(404, 'Arquivo não encontrado');
        }

        return Storage::disk('public')->download($atendimento->anexo);
    }
} 