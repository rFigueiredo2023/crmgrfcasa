<?php

namespace App\Http\Controllers;

use App\Models\Atendimento;
use App\Models\Cliente;
use App\Models\Lead;
use App\Models\Historico;
use App\Enums\StatusAtendimento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

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
            'tipo_contato' => 'required|in:Ligação,WhatsApp,E-mail,Visita,Reunião,Outro',
            'descricao' => 'required|string',
            'retorno' => 'nullable|string',
            'data_retorno' => 'nullable|date|after_or_equal:today',
            'proxima_acao' => 'nullable|string',
            'data_proxima_acao' => 'nullable|date|after_or_equal:today',
            'ativar_lembrete' => 'nullable|boolean',
            'anexo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:5120',
            'status' => ['required', Rule::in(StatusAtendimento::values())]
        ]);

        try {
            $data = $request->only([
                'cliente_id',
                'tipo_contato',
                'descricao',
                'retorno',
                'data_retorno',
                'proxima_acao',
                'data_proxima_acao',
                'ativar_lembrete',
                'status'
            ]);

            $data['user_id'] = auth()->id();

            $atendimento = Atendimento::create($data);

            if ($request->hasFile('anexo')) {
                $path = $request->file('anexo')->store('atendimentos/anexos', 'public');
                $atendimento->anexo = $path;
                $atendimento->save();
            }

            // Buscar o cliente e criar histórico via relacionamento polimórfico
            $cliente = Cliente::find($request->cliente_id);
            $cliente->historicos()->create([
                'user_id' => auth()->id(),
                'tipo' => $request->tipo_contato,
                'texto' => $request->descricao,
                'proxima_acao' => $request->proxima_acao,
                'data' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Atendimento registrado com sucesso',
                'atendimento' => [
                    'id' => $atendimento->id,
                    'cliente' => $atendimento->cliente->razao_social,
                    'vendedor' => auth()->user()->name,
                    'tipo' => $atendimento->tipo_contato,
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
            'tipo_contato' => 'required|in:Ligação,WhatsApp,E-mail,Visita,Reunião,Outro',
            'descricao' => 'required|string',
            'retorno' => 'nullable|string',
            'data_retorno' => 'nullable|date|after_or_equal:today',
            'proxima_acao' => 'nullable|string',
            'data_proxima_acao' => 'nullable|date|after_or_equal:today',
            'ativar_lembrete' => 'nullable|boolean',
            'anexo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:5120',
            'status' => ['required', Rule::in(StatusAtendimento::values())]
        ]);

        try {
            $atendimento->tipo_contato = $request->tipo_contato;
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

            // Atualiza o histórico do cliente usando relacionamento polimórfico
            $cliente = Cliente::find($atendimento->cliente_id);
            $cliente->historicos()->create([
                'user_id' => auth()->id(),
                'tipo' => $request->tipo_contato,
                'texto' => "Atendimento atualizado: " . $request->descricao,
                'proxima_acao' => $request->proxima_acao,
                'data' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Atendimento atualizado com sucesso',
                'atendimento' => [
                    'id' => $atendimento->id,
                    'cliente' => $atendimento->cliente->razao_social,
                    'vendedor' => auth()->user()->name,
                    'tipo' => $atendimento->tipo_contato,
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

    /**
     * Cria um novo lead com histórico de atendimento
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function storeLeadComAtendimento(Request $request)
    {
        try {
            DB::beginTransaction();

            // 1. Criar o Lead
            $lead = Lead::create([
                'razao_social' => $request->razao_social,
                'cnpj' => $request->cnpj,
                'ie' => $request->ie,
                'endereco' => $request->endereco,
                'telefone' => $request->telefone,
                'contato' => $request->contato,
                'user_id' => auth()->id()
            ]);

            // 2. Criar o Histórico usando relacionamento polimórfico
            $historico = $lead->historicos()->create([
                'user_id' => auth()->id(),
                'data' => now(),
                'tipo' => $request->tipo_contato,
                'texto' => $request->descricao,
                'proxima_acao' => $request->proxima_acao,
                'data_proxima_acao' => $request->data_proxima_acao,
                'retorno' => $request->retorno,
                'data_retorno' => $request->data_retorno,
                'ativar_lembrete' => $request->has('ativar_lembrete')
            ]);

            // Se tiver anexo, salvar
            if ($request->hasFile('anexo')) {
                $path = $request->file('anexo')->store('atendimentos/anexos', 'public');
                $historico->update(['anexo' => $path]);
            }

            DB::commit();

            // Se a requisição espera JSON, retorna resposta JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Lead e atendimento registrados com sucesso!'
                ]);
            }

            // Se não, redireciona com mensagem de sucesso
            return redirect()->route('atendimentos.index')
                ->with('success', 'Lead e atendimento registrados com sucesso!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Se a requisição espera JSON, retorna erro em JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao registrar lead e atendimento: ' . $e->getMessage()
                ], 500);
            }

            // Se não, redireciona com mensagem de erro
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Erro ao registrar lead e atendimento: ' . $e->getMessage()]);
        }
    }

    /**
     * Retorna todos os atendimentos de um cliente específico
     *
     * @param int $cliente_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAtendimentosByCliente($cliente_id)
    {
        try {
            $atendimentos = Atendimento::where('cliente_id', $cliente_id)
                ->select([
                    'id',
                    'tipo_contato',
                    'descricao',
                    'retorno',
                    'data_retorno',
                    'proxima_acao',
                    'data_proxima_acao',
                    'ativar_lembrete',
                    'status',
                    'created_at'
                ])
                ->orderBy('created_at', 'DESC')
                ->get()
                ->map(function ($atendimento) {
                    return [
                        'id' => $atendimento->id,
                        'tipo_contato' => $atendimento->tipo_contato,
                        'descricao' => $atendimento->descricao,
                        'retorno' => $atendimento->retorno,
                        'data_retorno' => $atendimento->data_retorno ?
                            Carbon::parse($atendimento->data_retorno)->format('d/m/Y H:i') : null,
                        'proxima_acao' => $atendimento->proxima_acao,
                        'data_proxima_acao' => $atendimento->data_proxima_acao ?
                            Carbon::parse($atendimento->data_proxima_acao)->format('d/m/Y H:i') : null,
                        'ativar_lembrete' => $atendimento->ativar_lembrete,
                        'status' => $atendimento->status,
                        'created_at' => Carbon::parse($atendimento->created_at)->format('d/m/Y H:i')
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $atendimentos
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar atendimentos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna os atendimentos de um cliente específico
     *
     * @param Cliente $cliente
     * @return JsonResponse
     */
    public function getByCliente(Cliente $cliente): JsonResponse
    {
        try {
            $atendimentos = Atendimento::where('cliente_id', $cliente->id)
                ->select([
                    'tipo_contato',
                    'descricao',
                    'retorno',
                    'data_retorno',
                    'proxima_acao',
                    'data_proxima_acao',
                    'ativar_lembrete',
                    'status',
                    'created_at'
                ])
                ->orderBy('created_at', 'DESC')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $atendimentos->map(function ($atendimento) {
                    return [
                        'tipo_contato' => $atendimento->tipo_contato,
                        'descricao' => $atendimento->descricao,
                        'retorno' => $atendimento->retorno,
                        'data_retorno' => $atendimento->data_retorno ?
                            Carbon::parse($atendimento->data_retorno)->format('d/m/Y H:i') : null,
                        'proxima_acao' => $atendimento->proxima_acao,
                        'data_proxima_acao' => $atendimento->data_proxima_acao ?
                            Carbon::parse($atendimento->data_proxima_acao)->format('d/m/Y H:i') : null,
                        'ativar_lembrete' => $atendimento->ativar_lembrete,
                        'status' => $atendimento->status,
                        'created_at' => Carbon::parse($atendimento->created_at)->format('d/m/Y H:i')
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar atendimentos do cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Listar históricos de um lead
    public function getLeadHistoricos($id)
    {
        $lead = Lead::find($id);
        $historicos = $lead->historicos;
        return response()->json([
            'success' => true,
            'data' => $historicos
        ]);
    }

    // Listar históricos de um cliente
    public function getClienteHistoricos($id)
    {
        $cliente = Cliente::find($id);
        $historicos = $cliente->historicos;
        return response()->json([
            'success' => true,
            'data' => $historicos
        ]);
    }

    // Criar novo histórico para um lead
    public function createLeadHistorico(Request $request)
    {
        try {
            DB::beginTransaction();

            // Criar o lead
            $lead = Lead::create([
                'razao_social' => $request->razao_social,
                'cnpj' => $request->cnpj,
                'ie' => $request->ie,
                'endereco' => $request->endereco,
                'telefone' => $request->telefone,
                'contato' => $request->contato,
                'user_id' => auth()->id()
            ]);

            // Criar o histórico usando o relacionamento polimórfico
            $lead->historicos()->create([
                'user_id' => auth()->id(),
                'data' => now(),
                'tipo' => $request->tipo_contato,
                'texto' => $request->descricao,
                'proxima_acao' => $request->proxima_acao,
                'data_proxima_acao' => $request->data_proxima_acao,
                'retorno' => $request->retorno,
                'data_retorno' => $request->data_retorno,
                'ativar_lembrete' => $request->ativar_lembrete ?? false
            ]);

            // Se tiver anexo, salvar
            if ($request->hasFile('anexo')) {
                // ... código para salvar anexo ...
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Lead e atendimento registrados com sucesso!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar lead e atendimento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function historico(Cliente $cliente)
    {
        try {
            \Log::info('Buscando histórico para o cliente ID: ' . $cliente->id);

            $atendimentos = $cliente->atendimentos()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            \Log::info('Atendimentos encontrados: ' . $atendimentos->count());

            if ($atendimentos->isEmpty()) {
                \Log::info('Nenhum atendimento encontrado para o cliente');
                return response()->json([]);
            }

            $dados = $atendimentos->map(function ($atendimento) {
                return [
                    'id' => $atendimento->id,
                    'tipo_contato' => $atendimento->tipo_contato,
                    'descricao' => $atendimento->descricao,
                    'data_atendimento' => $atendimento->created_at,
                    'status' => $atendimento->status,
                    'retorno' => $atendimento->retorno,
                    'data_retorno' => $atendimento->data_retorno,
                    'proxima_acao' => $atendimento->proxima_acao,
                    'vendedor' => $atendimento->user->name ?? 'Não atribuído'
                ];
            });

            \Log::info('Dados formatados com sucesso');
            return response()->json($dados);

        } catch (\Exception $e) {
            \Log::error('Erro ao carregar histórico de atendimentos: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Erro ao carregar os atendimentos: ' . $e->getMessage()], 500);
        }
    }
}
