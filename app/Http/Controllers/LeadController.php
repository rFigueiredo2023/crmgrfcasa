<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class LeadController extends Controller
{
    public function index()
    {
        $leads = Lead::with('vendedor')->get();
        return view('content.pages.leads.pages-leads', compact('leads'));
    }

    public function historico(Lead $lead)
    {
        try {
            // Log detalhado para diagnóstico
            \Log::info('Função historico() chamada para lead ID: ' . $lead->id, [
                'lead_data' => [
                    'id' => $lead->id,
                    'razao_social' => $lead->razao_social,
                    'has_historicos_relation' => method_exists($lead, 'historicos')
                ]
            ]);

            // Verificar se o lead existe
            if (!$lead) {
                throw new \Exception("Lead não encontrado");
            }

            try {
                // Primeiro tenta buscar históricos
                $historicos = $lead->historicos()
                    ->with('user')
                    ->orderBy('data', 'desc')
                    ->get();

                \Log::info('Históricos encontrados para lead ID ' . $lead->id . ': ' . $historicos->count());

                $historicosFormatados = $historicos->map(function($historico) {
                    try {
                        return [
                            'id' => $historico->id,
                            'tipo' => $historico->tipo,
                            'data' => $historico->data->format('d/m/Y H:i'),
                            'texto' => $historico->texto,
                            'vendedora' => $historico->user ? $historico->user->name : 'Não atribuído',
                            'status' => 'em_andamento',
                            'retorno' => $historico->retorno,
                            'data_retorno' => $historico->data_retorno ? $historico->data_retorno->format('d/m/Y \à\s H:i') : null,
                            'proxima_acao' => $historico->proxima_acao,
                            'data_proxima_acao' => $historico->data_proxima_acao ? $historico->data_proxima_acao->format('d/m/Y \à\s H:i') : null,
                            'ativar_lembrete' => $historico->ativar_lembrete,
                            'anexo' => $historico->anexo ? (Storage::exists($historico->anexo) ? Storage::url($historico->anexo) : null) : null,
                            'vendedor' => $historico->user ? [
                                'id' => $historico->user->id,
                                'name' => $historico->user->name
                            ] : null
                        ];
                    } catch (\Exception $e) {
                        \Log::error('Erro ao processar histórico ID ' . $historico->id . ': ' . $e->getMessage());
                        return null;
                    }
                })->filter();

                // Retorna os dados formatados
                return response()->json([
                    'success' => true,
                    'cliente' => [
                        'razao_social' => $lead->razao_social,
                        'cnpj' => $lead->cnpj ?? 'Não informado',
                        'telefone' => $lead->telefone,
                        'contato' => $lead->contato,
                        'endereco' => $lead->endereco ?? 'Não informado',
                        'vendedora' => $lead->vendedor ? $lead->vendedor->name : 'Não atribuído'
                    ],
                    'historicos' => $historicosFormatados,
                    // Inclui mensagem apropriada
                    'message' => $historicosFormatados->count() > 0
                        ? 'Históricos carregados com sucesso'
                        : 'Nenhum histórico encontrado para este lead'
                ]);

            } catch (\Exception $e) {
                // Erro específico ao acessar os históricos
                \Log::error('Erro ao acessar históricos do lead: ' . $e->getMessage());

                // Mesmo que ocorra um erro, retornamos uma resposta válida para evitar quebrar a interface
                return response()->json([
                    'success' => true,
                    'cliente' => [
                        'razao_social' => $lead->razao_social,
                        'cnpj' => $lead->cnpj ?? 'Não informado',
                        'telefone' => $lead->telefone,
                        'contato' => $lead->contato,
                        'endereco' => $lead->endereco ?? 'Não informado',
                        'vendedora' => $lead->vendedor ? $lead->vendedor->name : 'Não atribuído'
                    ],
                    'historicos' => [],
                    'message' => 'Erro ao carregar históricos: ' . $e->getMessage(),
                    'debug_info' => 'Verifique se os relacionamentos polimórficos estão configurados corretamente. Use o comando "php artisan historicos:fix" para corrigir problemas.'
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Erro no historico de lead: ' . $e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Ocorreu um erro ao processar o histórico de lead.',
                'debug_info' => 'Verifique se os relacionamentos polimórficos estão configurados corretamente. Use o comando "php artisan historicos:fix" para corrigir problemas.'
            ], 500);
        }
    }

    public function storeHistorico(Request $request, Lead $lead)
    {
        $validated = $request->validate([
            'texto' => 'required|string',
            'tipo' => 'required|string|in:Ligação,WhatsApp,E-mail,Visita,Reunião,Outro',
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
                'texto' => $validated['texto'],
                'proxima_acao' => $validated['proxima_acao'],
                'data_proxima_acao' => $validated['data_proxima_acao'],
                'retorno' => $validated['retorno'],
                'data_retorno' => $validated['data_retorno'],
                'ativar_lembrete' => $request->boolean('ativar_lembrete'),
                'anexo' => $anexoPath
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Histórico registrado com sucesso!',
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
                    'anexo' => $anexoPath ? Storage::url($anexoPath) : null
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar histórico: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'razao_social' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:18',
            'ie' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'codigo_ibge' => 'nullable|string|max:7',
            'telefone' => 'required|string|max:20',
            'contato' => 'nullable|string|max:255',
            'data_proxima_acao' => 'nullable|date',
            'data_retorno' => 'nullable|date',
            'ativar_lembrete' => 'nullable|boolean',
            'user_id' => 'nullable|exists:users,id'
        ]);

        $lead = Lead::create([
            'razao_social' => $request->razao_social,
            'cnpj' => $request->cnpj,
            'ie' => $request->ie,
            'endereco' => $request->endereco,
            'codigo_ibge' => $request->codigo_ibge,
            'telefone' => $request->telefone,
            'contato' => $request->contato,
            'data_proxima_acao' => $request->data_proxima_acao,
            'data_retorno' => $request->data_retorno,
            'ativar_lembrete' => $request->boolean('ativar_lembrete'),
            'user_id' => $request->user_id ?? auth()->id()
        ]);

        return redirect()->back()->with('success', 'Lead cadastrado com sucesso!');
    }

    public function update(Request $request, Lead $lead)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'origem' => 'required|string|max:255',
            'status' => 'required|in:Frio,Morno,Quente',
            'observacoes' => 'nullable|string'
        ]);

        $lead->update($request->all());

        return redirect()->back()->with('success', 'Lead atualizado com sucesso!');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->back()->with('success', 'Lead excluído com sucesso!');
    }

    public function converter(Lead $lead)
    {
        try {
            DB::beginTransaction();

            // Criar o cliente
            $cliente = Cliente::create([
                'razao_social' => $lead->razao_social,
                'cnpj' => $lead->cnpj,
                'ie' => $lead->ie,
                'endereco' => $lead->endereco,
                'codigo_ibge' => $lead->codigo_ibge,
                'telefone' => $lead->telefone,
                'contato' => $lead->contato,
                'user_id' => auth()->id()
            ]);

            // Copiar históricos - seguindo o padrão polimórfico
            foreach ($lead->historicos as $historico) {
                // Se tiver anexo, copiar para novo diretório com o ID do cliente
                $novoAnexoPath = null;
                if ($historico->anexo) {
                    $oldPath = str_replace('public/', '', $historico->anexo);
                    if (Storage::disk('public')->exists($oldPath)) {
                        $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
                        $novoAnexoPath = "historicos/anexos/cliente_{$cliente->id}_" . uniqid() . ".{$extension}";
                        Storage::disk('public')->copy($oldPath, $novoAnexoPath);
                    }
                }

                // Criar novo histórico para o cliente com os dados do lead
                // Não usamos toArray() para evitar copiar IDs e campos de controle
                $cliente->historicos()->create([
                    'user_id' => $historico->user_id,
                    'data' => $historico->data,
                    'tipo' => $historico->tipo,
                    'texto' => $historico->texto,
                    'proxima_acao' => $historico->proxima_acao,
                    'data_proxima_acao' => $historico->data_proxima_acao,
                    'retorno' => $historico->retorno,
                    'data_retorno' => $historico->data_retorno,
                    'ativar_lembrete' => $historico->ativar_lembrete,
                    'anexo' => $novoAnexoPath
                    // historicable_id e historicable_type são preenchidos automaticamente
                    // pela relação polimórfica $cliente->historicos()
                ]);
            }

            // Deletar o lead e seus anexos
            foreach ($lead->historicos as $historico) {
                if ($historico->anexo) {
                    $oldPath = str_replace('public/', '', $historico->anexo);
                    Storage::disk('public')->delete($oldPath);
                }
            }
            $lead->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lead convertido em cliente com sucesso!',
                'cliente_id' => $cliente->id
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao converter lead em cliente: ' . $e->getMessage()
            ], 500);
        }
    }
}
