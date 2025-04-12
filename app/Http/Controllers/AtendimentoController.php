<?php

namespace App\Http\Controllers;

use App\Models\Atendimento;
use App\Models\Cliente;
use App\Models\Lead;
use App\Models\Historico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AtendimentoController extends Controller
{
    public function index()
    {
        $atendimentos = Atendimento::with(['vendedor', 'cliente'])->get();
        $clientes = Cliente::all();
        $leads = Lead::all();

        return view('content.pages.atendimentos.pages-atendimentos',
            compact('atendimentos', 'clientes', 'leads')
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo' => 'required|in:Ligação,WhatsApp,E-mail,Visita,Reunião,Outro',
            'descricao' => 'required|string',
            'retorno' => 'nullable|string',
            'data_retorno' => 'nullable|date|after_or_equal:today',
            'proxima_acao' => 'nullable|string',
            'data_proxima_acao' => 'nullable|date|after_or_equal:today',
            'ativar_lembrete' => 'nullable|boolean',
            'anexo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:5120',
            'status' => 'required|in:Aberto,Em Andamento,Concluído'
        ]);

        try {
            $atendimento = new Atendimento();
            $atendimento->cliente_id = $request->cliente_id;
            $atendimento->user_id = auth()->id();
            $atendimento->tipo = $request->tipo;
            $atendimento->descricao = $request->descricao;
            $atendimento->retorno = $request->retorno;
            $atendimento->data_retorno = $request->data_retorno;
            $atendimento->proxima_acao = $request->proxima_acao;
            $atendimento->data_proxima_acao = $request->data_proxima_acao;
            $atendimento->ativar_lembrete = $request->boolean('ativar_lembrete');
            $atendimento->status = $request->status;

            if ($request->hasFile('anexo')) {
                $path = $request->file('anexo')->store('atendimentos/anexos', 'public');
                $atendimento->anexo = $path;
            }

            $atendimento->save();

            // Criar histórico do cliente
            $historico = new Historico();
            $historico->cliente_id = $request->cliente_id;
            $historico->user_id = auth()->id();
            $historico->tipo = $request->tipo;
            $historico->texto = $request->descricao;
            $historico->proxima_acao = $request->proxima_acao;
            $historico->data = now();
            $historico->save();

            return response()->json([
                'success' => true,
                'message' => 'Atendimento registrado com sucesso',
                'atendimento' => [
                    'id' => $atendimento->id,
                    'cliente' => $atendimento->cliente->razao_social,
                    'vendedor' => auth()->user()->name,
                    'tipo' => $atendimento->tipo,
                    'descricao' => $atendimento->descricao,
                    'retorno' => $atendimento->retorno,
                    'data_retorno' => $atendimento->data_retorno ? $atendimento->data_retorno->format('d/m/Y') : null,
                    'proxima_acao' => $atendimento->proxima_acao,
                    'data_proxima_acao' => $atendimento->data_proxima_acao ? $atendimento->data_proxima_acao->format('d/m/Y') : null,
                    'status' => $atendimento->status,
                    'created_at' => $atendimento->created_at->format('d/m/Y H:i')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar atendimento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function search(Request $request)
    {
        $query = Atendimento::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('cliente', function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%");
            })
            ->orWhere('tipo_atendimento', 'like', "%{$search}%")
            ->orWhere('status', 'like', "%{$search}%");
        }

        $atendimentos = $query->with(['vendedor', 'cliente'])->get();
        return view('content.pages.atendimentos.pages-atendimentos', compact('atendimentos'));
    }

    public function update(Request $request, Atendimento $atendimento)
    {
        $request->validate([
            'tipo' => 'required|in:Ligação,WhatsApp,E-mail,Visita,Reunião,Outro',
            'descricao' => 'required|string',
            'retorno' => 'nullable|string',
            'data_retorno' => 'nullable|date|after_or_equal:today',
            'proxima_acao' => 'nullable|string',
            'data_proxima_acao' => 'nullable|date|after_or_equal:today',
            'ativar_lembrete' => 'nullable|boolean',
            'anexo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:5120',
            'status' => 'required|in:Aberto,Em Andamento,Concluído'
        ]);

        try {
            $atendimento->tipo = $request->tipo;
            $atendimento->descricao = $request->descricao;
            $atendimento->retorno = $request->retorno;
            $atendimento->data_retorno = $request->data_retorno;
            $atendimento->proxima_acao = $request->proxima_acao;
            $atendimento->data_proxima_acao = $request->data_proxima_acao;
            $atendimento->ativar_lembrete = $request->boolean('ativar_lembrete');
            $atendimento->status = $request->status;

            if ($request->hasFile('anexo')) {
                // Remove o anexo antigo se existir
                if ($atendimento->anexo) {
                    Storage::disk('public')->delete($atendimento->anexo);
                }
                
                $path = $request->file('anexo')->store('atendimentos/anexos', 'public');
                $atendimento->anexo = $path;
            }

            $atendimento->save();

            // Atualiza o histórico do cliente
            $historico = new Historico();
            $historico->cliente_id = $atendimento->cliente_id;
            $historico->user_id = auth()->id();
            $historico->tipo = $request->tipo;
            $historico->texto = "Atendimento atualizado: " . $request->descricao;
            $historico->proxima_acao = $request->proxima_acao;
            $historico->data = now();
            $historico->save();

            return response()->json([
                'success' => true,
                'message' => 'Atendimento atualizado com sucesso',
                'atendimento' => [
                    'id' => $atendimento->id,
                    'cliente' => $atendimento->cliente->razao_social,
                    'vendedor' => auth()->user()->name,
                    'tipo' => $atendimento->tipo,
                    'descricao' => $atendimento->descricao,
                    'retorno' => $atendimento->retorno,
                    'data_retorno' => $atendimento->data_retorno ? $atendimento->data_retorno->format('d/m/Y') : null,
                    'proxima_acao' => $atendimento->proxima_acao,
                    'data_proxima_acao' => $atendimento->data_proxima_acao ? $atendimento->data_proxima_acao->format('d/m/Y') : null,
                    'status' => $atendimento->status,
                    'updated_at' => $atendimento->updated_at->format('d/m/Y H:i')
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar atendimento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeLeadComAtendimento(Request $request)
    {
        $request->validate([
            'nome_empresa' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:18',
            'ie' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'codigo_ibge' => 'nullable|string|max:7',
            'telefone' => 'required|string|max:20',
            'contato' => 'required|string|max:255',
            'tipo' => 'required|in:Ligação,WhatsApp,E-mail,Visita,Reunião,Outro',
            'descricao' => 'required|string',
            'retorno' => 'nullable|string',
            'data_retorno' => 'nullable|date|after_or_equal:today',
            'proxima_acao' => 'nullable|string',
            'data_proxima_acao' => 'nullable|date|after_or_equal:today',
            'ativar_lembrete' => 'nullable|boolean',
            'anexo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:5120'
        ]);

        try {
            DB::beginTransaction();

            // Criar o lead
            $lead = Lead::create([
                'nome_empresa' => $request->nome_empresa,
                'cnpj' => $request->cnpj,
                'ie' => $request->ie,
                'endereco' => $request->endereco,
                'codigo_ibge' => $request->codigo_ibge,
                'telefone' => $request->telefone,
                'contato' => $request->contato,
                'data_proxima_acao' => $request->data_proxima_acao,
                'data_retorno' => $request->data_retorno,
                'ativar_lembrete' => $request->boolean('ativar_lembrete'),
                'user_id' => auth()->id()
            ]);

            // Criar o histórico do lead
            $historico = $lead->historicos()->create([
                'user_id' => auth()->id(),
                'data' => now(),
                'tipo' => $request->tipo,
                'texto' => $request->descricao,
                'proxima_acao' => $request->proxima_acao,
                'data_proxima_acao' => $request->data_proxima_acao,
                'retorno' => $request->retorno,
                'data_retorno' => $request->data_retorno,
                'ativar_lembrete' => $request->boolean('ativar_lembrete')
            ]);

            // Se houver anexo, salvar
            if ($request->hasFile('anexo')) {
                $path = $request->file('anexo')->store('historicos/anexos', 'public');
                $historico->anexo = $path;
                $historico->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lead e atendimento registrados com sucesso!',
                'lead' => [
                    'id' => $lead->id,
                    'nome_empresa' => $lead->nome_empresa,
                    'telefone' => $lead->telefone,
                    'contato' => $lead->contato,
                    'vendedor' => auth()->user()->name
                ],
                'historico' => [
                    'data' => $historico->data->format('d/m/Y H:i'),
                    'tipo' => $historico->tipo,
                    'texto' => $historico->texto,
                    'proxima_acao' => $historico->proxima_acao,
                    'data_proxima_acao' => $historico->data_proxima_acao ? $historico->data_proxima_acao->format('d/m/Y') : null,
                    'retorno' => $historico->retorno,
                    'data_retorno' => $historico->data_retorno ? $historico->data_retorno->format('d/m/Y') : null
                ]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar lead e atendimento: ' . $e->getMessage()
            ], 500);
        }
    }
}