<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::with('vendedor')->get();
        return view('content.pages.customers.pages-customers', compact('clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'razao_social' => 'required|string|max:255',
            'cnpj' => 'required|string|max:18|unique:clientes,cnpj',
            'inscricao_estadual' => 'nullable|string|max:20',
            'endereco' => 'required|string|max:255',
            'codigo_ibge' => 'required|string|max:10',
            'telefone' => 'required|string|max:20',
            'contato' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'cep' => 'nullable|string|max:10',
            'municipio' => 'required|string|max:100',
            'uf' => 'required|string|max:2'
        ]);

        $cliente = Cliente::create([
            'razao_social' => $request->razao_social,
            'cnpj' => $request->cnpj,
            'inscricao_estadual' => $request->inscricao_estadual,
            'endereco' => $request->endereco,
            'codigo_ibge' => $request->codigo_ibge,
            'telefone' => $request->telefone,
            'contato' => $request->contato,
            'email' => $request->email,
            'cep' => $request->cep,
            'municipio' => $request->municipio,
            'uf' => $request->uf,
            'user_id' => auth()->id() // Registra o usuário que está cadastrando
        ]);

        return redirect()->back()->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function detalhes(Cliente $cliente)
    {
        $cliente->load(['atendimentos.vendedor', 'arquivos.usuario', 'mensagens.usuario']);

        return response()->json([
            'razao_social' => $cliente->razao_social,
            'cnpj' => $cliente->cnpj,
            'telefone' => $cliente->telefone,
            'contato' => $cliente->contato,
            'interacoes' => $cliente->atendimentos->map(function($atendimento) {
                return [
                    'data' => $atendimento->data_atendimento->format('d/m/Y H:i'),
                    'descricao' => $atendimento->descricao,
                    'tipo' => $atendimento->tipo_atendimento,
                    'status' => $atendimento->status,
                    'vendedor' => $atendimento->vendedor->name
                ];
            }),
            'arquivos' => $cliente->arquivos->map(function($arquivo) {
                return [
                    'id' => $arquivo->id,
                    'nome' => $arquivo->nome_original,
                    'tamanho' => $arquivo->tamanho,
                    'tipo' => $arquivo->tipo,
                    'url' => Storage::url($arquivo->caminho),
                    'data' => $arquivo->created_at->format('d/m/Y H:i'),
                    'usuario' => $arquivo->usuario->name
                ];
            }),
            'mensagens' => $cliente->mensagens->map(function($mensagem) {
                return [
                    'id' => $mensagem->id,
                    'usuario' => $mensagem->usuario->name,
                    'mensagem' => $mensagem->conteudo,
                    'tipo' => $mensagem->user_id === auth()->id() ? 'sent' : 'received',
                    'data' => $mensagem->created_at->format('d/m/Y H:i'),
                    'lida' => $mensagem->lida
                ];
            })
        ]);
    }

    public function edit(Cliente $cliente)
    {
        try {
            return response()->json($cliente);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao carregar dados do cliente'], 500);
        }
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'razao_social' => 'required|string|max:255',
            'cnpj' => 'required|string|max:18|unique:clientes,cnpj,'.$cliente->id,
            'inscricao_estadual' => 'nullable|string|max:20',
            'endereco' => 'required|string|max:255',
            'codigo_ibge' => 'required|string|max:10',
            'telefone' => 'required|string|max:20',
            'contato' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'cep' => 'nullable|string|max:10',
            'municipio' => 'required|string|max:100',
            'uf' => 'required|string|max:2'
        ]);

        $cliente->update($validated);

        return redirect()->back()->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->back()->with('success', 'Cliente excluído com sucesso!');
    }

    public function historico(Cliente $cliente)
    {
        try {
            $historicos = $cliente->historicos()->with('usuario')->orderBy('data', 'desc')->get()->map(function($historico) {
                return [
                    'data' => $historico->data->format('d/m/Y H:i'),
                    'vendedora' => $historico->usuario->name,
                    'texto' => $historico->texto,
                    'proxima_acao' => $historico->proxima_acao
                ];
            });

            return response()->json([
                'success' => true,
                'cliente' => [
                    'razao_social' => $cliente->razao_social,
                    'cnpj' => $cliente->cnpj,
                    'telefone' => $cliente->telefone,
                    'contato' => $cliente->contato,
                    'endereco' => $cliente->endereco,
                    'vendedora' => $cliente->vendedor->name ?? 'Não atribuído'
                ],
                'historicos' => $historicos
            ]);
        } catch (\Exception $e) {
            // Se ocorrer um erro (talvez porque o ID é de um lead, não de um cliente)
            \Log::error('Erro ao carregar histórico: ' . $e->getMessage());

            // Envie uma resposta com array vazio para não quebrar o frontend
            return response()->json([
                'success' => true,
                'cliente' => [
                    'razao_social' => 'Lead',
                    'cnpj' => 'N/A',
                    'telefone' => 'N/A',
                    'contato' => 'N/A',
                    'endereco' => 'N/A',
                    'vendedora' => 'N/A'
                ],
                'historicos' => []
            ]);
        }
    }

    public function storeHistorico(Request $request, Cliente $cliente)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'tipo_contato' => 'required|string',
                'texto' => 'required|string',
                'proxima_acao' => 'nullable|string',
                'data_proxima_acao' => 'nullable|date',
                'retorno' => 'nullable|string',
                'data_retorno' => 'nullable|date',
                'ativar_lembrete' => 'nullable|boolean',
                'anexo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:5120'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Criar o histórico usando o relacionamento polimórfico
            $historico = $cliente->historicos()->create([
                'user_id' => auth()->id(),
                'tipo' => $request->tipo_contato,
                'texto' => $request->texto,
                'proxima_acao' => $request->proxima_acao,
                'data_proxima_acao' => $request->data_proxima_acao,
                'retorno' => $request->retorno,
                'data_retorno' => $request->data_retorno,
                'ativar_lembrete' => $request->boolean('ativar_lembrete'),
                'data' => now()
            ]);

            // Se tiver anexo, salvar
            if ($request->hasFile('anexo')) {
                $path = $request->file('anexo')->store('atendimentos/anexos', 'public');
                $historico->anexo = $path;
                $historico->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Histórico registrado com sucesso!',
                'historico' => $historico
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar histórico: ' . $e->getMessage()
            ], 500);
        }
    }

    public function atendimentos(Cliente $cliente)
    {
        try {
            $atendimentos = $cliente->atendimentos()
                ->with('vendedor')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($atendimento) {
                    return [
                        'id' => $atendimento->id,
                        'tipo_contato' => $atendimento->tipo_contato,
                        'status' => $atendimento->status,
                        'descricao' => $atendimento->descricao,
                        'retorno' => $atendimento->retorno,
                        'data_retorno' => $atendimento->data_retorno,
                        'proxima_acao' => $atendimento->proxima_acao,
                        'data_proxima_acao' => $atendimento->data_proxima_acao,
                        'data_atendimento' => $atendimento->data_atendimento ?? $atendimento->created_at,
                        'anexo' => $atendimento->anexo,
                        'created_at' => $atendimento->created_at,
                        'vendedor' => $atendimento->vendedor ? $atendimento->vendedor->name : 'Não atribuído'
                    ];
                });

            return response()->json($atendimentos);
        } catch (\Exception $e) {
            \Log::error('Erro ao buscar atendimentos: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    /**
     * Retornar dados de um cliente específico para API
     *
     * @param Cliente $cliente
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Cliente $cliente)
    {
        try {
            return response()->json($cliente);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao buscar cliente: ' . $e->getMessage()], 500);
        }
    }
}
