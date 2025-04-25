<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreClienteRequest;
use App\Http\Requests\StoreHistoricoRequest;
use App\Http\Resources\ClienteResource;
use App\Models\Cliente;
use App\Services\ClienteService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;

class ClienteController extends Controller
{
    /**
     * @var ClienteService
     */
    protected ClienteService $clienteService;

    /**
     * Construtor com injeção do serviço de clientes
     */
    public function __construct(ClienteService $clienteService)
    {
        $this->clienteService = $clienteService;
    }

    /**
     * Exibe a página de listagem de clientes
     */
    public function index(): View
    {
        $clientes = $this->clienteService->buscarTodos();
        return view('content.pages.customers.pages-customers', compact('clientes'));
    }

    /**
     * Salva um novo cliente
     */
    public function store(StoreClienteRequest $request): RedirectResponse
    {
        try {
            $this->clienteService->create($request->validated());
            return redirect()->back()->with('success', 'Cliente cadastrado com sucesso!');
        } catch (Exception $e) {
            Log::error('Erro ao criar cliente: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao cadastrar cliente: ' . $e->getMessage());
        }
    }

    /**
     * Exibe detalhes do cliente, incluindo atendimentos, arquivos e mensagens
     */
    public function detalhes(Cliente $cliente): JsonResponse
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

    /**
     * Retorna dados do cliente para edição
     */
    public function edit(Cliente $cliente): JsonResponse
    {
        try {
            return response()->json($cliente->toArray());
        } catch (Exception $e) {
            Log::error('Erro ao carregar dados do cliente: ' . $e->getMessage(), [
                'cliente_id' => $cliente->id,
                'exception' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Erro ao carregar dados do cliente: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Atualiza um cliente existente
     */
    public function update(StoreClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        try {
            $this->clienteService->update($cliente, $request->validated());
            return redirect()->back()->with('success', 'Cliente atualizado com sucesso!');
        } catch (Exception $e) {
            Log::error('Erro ao atualizar cliente: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao atualizar cliente: ' . $e->getMessage());
        }
    }

    /**
     * Remove um cliente
     */
    public function destroy(Cliente $cliente): RedirectResponse
    {
        try {
            $this->clienteService->excluir($cliente);
            return redirect()->back()->with('success', 'Cliente excluído com sucesso!');
        } catch (Exception $e) {
            Log::error('Erro ao excluir cliente: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao excluir cliente: ' . $e->getMessage());
        }
    }

    /**
     * Mostra o histórico de um cliente
     */
    public function historico(Cliente $cliente): JsonResponse
    {
        try {
            $cliente->load(['historicos.usuario', 'vendedor']);

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
                'historicos' => $cliente->historicos->map(function($historico) {
                    return [
                        'data' => $historico->data->format('d/m/Y H:i'),
                        'vendedora' => $historico->usuario->name,
                        'texto' => $historico->texto,
                        'proxima_acao' => $historico->proxima_acao
                    ];
                })
            ]);
        } catch (Exception $e) {
            Log::error('Erro ao carregar histórico de cliente: ' . $e->getMessage());

            // Envie uma resposta com array vazio para não quebrar o frontend
            return response()->json([
                'success' => true,
                'cliente' => [
                    'razao_social' => 'Cliente',
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

    /**
     * Adiciona um histórico ao cliente
     */
    public function storeHistorico(StoreHistoricoRequest $request, Cliente $cliente): JsonResponse
    {
        try {
            $historico = $this->clienteService->adicionarHistorico($cliente, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Histórico registrado com sucesso!',
                'historico' => $historico
            ]);
        } catch (Exception $e) {
            Log::error('Erro ao registrar histórico: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar histórico: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna os atendimentos de um cliente
     */
    public function atendimentos(Cliente $cliente): JsonResponse
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
        } catch (Exception $e) {
            Log::error('Erro ao buscar atendimentos: ' . $e->getMessage());
            return response()->json([]);
        }
    }

    /**
     * Retorna dados de um cliente para API
     */
    public function show(Cliente $cliente): ClienteResource
    {
        $cliente->load(['vendedor', 'historicos', 'atendimentos', 'segmento']);
        return new ClienteResource($cliente);
    }

    /**
     * Consulta CNPJ em API externa
     */
    public function consultarCnpj(string $cnpj): JsonResponse
    {
        try {
            $data = $this->clienteService->consultarCnpj($cnpj);
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {
            Log::error('Controller: Erro ao consultar CNPJ: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar CNPJ: ' . $e->getMessage()
            ], 500);
        }
    }
}
