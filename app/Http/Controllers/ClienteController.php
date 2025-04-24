<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
            'telefone2' => 'nullable|string|max:20',
            'site' => 'nullable|url|max:255',
            'contato' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'cep' => 'nullable|string|max:10',
            'municipio' => 'required|string|max:100',
            'uf' => 'required|string|max:2',
            'segmento' => 'nullable|string|max:100',
            'segmento_id' => 'nullable|exists:segmentos,id',
            'atividade_principal' => 'nullable|string|max:255',
            'atividades_secundarias' => 'nullable|string'
        ]);

        $cliente = Cliente::create([
            'razao_social' => $request->razao_social,
            'cnpj' => $request->cnpj,
            'inscricao_estadual' => $request->inscricao_estadual,
            'endereco' => $request->endereco,
            'codigo_ibge' => $request->codigo_ibge,
            'telefone' => $request->telefone,
            'telefone2' => $request->telefone2,
            'site' => $request->site,
            'contato' => $request->contato,
            'email' => $request->email,
            'cep' => $request->cep,
            'municipio' => $request->municipio,
            'uf' => $request->uf,
            'segmento' => $request->segmento,
            'segmento_id' => $request->segmento_id,
            'atividade_principal' => $request->atividade_principal,
            'atividades_secundarias' => $request->atividades_secundarias,
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
            // Log para debug
            \Log::info('Iniciando carregamento de cliente para edição', ['cliente_id' => $cliente->id]);

            // Primeiro retorna apenas os dados básicos sem relacionamentos
            $clienteData = $cliente->toArray();

            // Log dos dados que estão sendo retornados
            \Log::info('Dados do cliente preparados para retorno', [
                'cliente_id' => $cliente->id,
                'fields' => array_keys($clienteData)
            ]);

            return response()->json($clienteData);
        } catch (\Exception $e) {
            \Log::error('Erro ao carregar dados do cliente: ' . $e->getMessage(), [
                'cliente_id' => $cliente->id ?? 'não definido',
                'exception_class' => get_class($e),
                'exception_message' => $e->getMessage(),
                'exception_trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Erro ao carregar dados do cliente: ' . $e->getMessage()], 500);
        }
    }

    public function update(Request $request, Cliente $cliente)
    {
        $request->validate([
            'razao_social' => 'required|string|max:255',
            'cnpj' => 'required|string|max:18|unique:clientes,cnpj,' . $cliente->id,
            'inscricao_estadual' => 'nullable|string|max:20',
            'endereco' => 'required|string|max:255',
            'codigo_ibge' => 'required|string|max:10',
            'telefone' => 'required|string|max:20',
            'telefone2' => 'nullable|string|max:20',
            'site' => 'nullable|url|max:255',
            'contato' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'cep' => 'nullable|string|max:10',
            'municipio' => 'required|string|max:100',
            'uf' => 'required|string|max:2',
            'segmento' => 'nullable|string|max:100',
            'segmento_id' => 'nullable|exists:segmentos,id',
            'atividade_principal' => 'nullable|string|max:255',
            'atividades_secundarias' => 'nullable|string'
        ]);

        $cliente->update([
            'razao_social' => $request->razao_social,
            'cnpj' => $request->cnpj,
            'inscricao_estadual' => $request->inscricao_estadual,
            'endereco' => $request->endereco,
            'codigo_ibge' => $request->codigo_ibge,
            'telefone' => $request->telefone,
            'telefone2' => $request->telefone2,
            'site' => $request->site,
            'contato' => $request->contato,
            'email' => $request->email,
            'cep' => $request->cep,
            'municipio' => $request->municipio,
            'uf' => $request->uf,
            'segmento' => $request->segmento,
            'segmento_id' => $request->segmento_id,
            'atividade_principal' => $request->atividade_principal,
            'atividades_secundarias' => $request->atividades_secundarias
        ]);

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
            Log::error('Erro ao carregar histórico: ' . $e->getMessage());

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
            Log::error('Erro ao buscar atendimentos: ' . $e->getMessage());
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

    /**
     * Método para consultar um CNPJ na API CNPJa
     */
    public function consultarCnpj(Request $request)
    {
        try {
            // Obtém o CNPJ da requisição e remove caracteres não numéricos
            $cnpj = preg_replace('/\D/', '', $request->cnpj);

            // Verifica se o CNPJ tem 14 dígitos
            if (strlen($cnpj) !== 14) {
                return response()->json([
                    'success' => false,
                    'message' => 'CNPJ inválido. Deve conter 14 dígitos.'
                ], 400);
            }

            Log::info('Controller: Iniciando consulta CNPJa', ['cnpj' => $cnpj]);

            try {
                // Token de autenticação da API CNPJa
                $apiToken = trim(config('services.cnpja.token'));
                $baseUrl = trim(config('services.cnpja.base_url', 'https://api.cnpja.com'));

                Log::info('Controller: Configuração CNPJa', [
                    'token_length' => strlen($apiToken),
                    'token_first_chars' => substr($apiToken, 0, 5) . '...',
                    'base_url' => $baseUrl
                ]);

                // Tentativa de fazer a requisição para a API CNPJa
                $url = "{$baseUrl}/office/{$cnpj}?registrations=BR&suframa=true";
                Log::info('Controller: Enviando requisição para CNPJa', ['url' => $url]);

                $response = Http::withHeaders([
                    'Authorization' => $apiToken
                ])->get($url);

                // Log da resposta HTTP (status e headers)
                Log::info('Controller: Resposta HTTP da API CNPJa', [
                    'status' => $response->status(),
                    'headers' => $response->headers(),
                    'successful' => $response->successful(),
                    'failed' => $response->failed()
                ]);

                // Se a resposta contiver erro, registre o corpo da resposta
                if (!$response->successful()) {
                    Log::error('Controller: Erro na resposta CNPJa', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'json' => $response->json()
                    ]);

                    // Log específico do corpo da resposta para melhor diagnóstico
                    Log::error('Controller: Corpo detalhado da resposta de erro CNPJa', [
                        'raw_body' => $response->body(),
                        'response_object' => json_encode($response)
                    ]);

                    // Mensagem personalizada para erro de autenticação
                    if ($response->status() === 401) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Erro de autenticação na API CNPJa. O token de acesso pode estar inválido ou expirado. Por favor, verifique as configurações da API e renove o token se necessário.',
                            'error_code' => 'token_invalid',
                            'api_response' => $response->json()
                        ], 500);
                    }

                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao consultar CNPJ: HTTP ' . $response->status() .
                                    ' - ' . ($response->json()['message'] ?? $response->body())
                    ], $response->status());
                }

                // Log de sucesso com informações básicas da resposta
                $data = $response->json();
                Log::info('Controller: Consulta CNPJa bem-sucedida', [
                    'razao_social' => $data['company']['name'] ?? 'N/A',
                    'situacao' => $data['status']['text'] ?? 'N/A'
                ]);

                return response()->json([
                    'success' => true,
                    'data' => $data
                ]);
            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                Log::error('Controller: Erro de conexão com a API CNPJa', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Erro de conexão com a API CNPJa: ' . $e->getMessage()
                ], 500);
            } catch (\Illuminate\Http\Client\RequestException $e) {
                Log::error('Controller: Erro na requisição para API CNPJa', [
                    'message' => $e->getMessage(),
                    'response' => $e->response ? $e->response->body() : 'Sem resposta',
                    'status' => $e->response ? $e->response->status() : 'Sem status',
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Erro na requisição para API CNPJa: ' . $e->getMessage()
                ], 500);
            } catch (\Exception $e) {
                // Captura exceções específicas dentro do contexto de conexão com a API
                Log::error('Controller: Exceção durante comunicação com a API CNPJa', [
                    'message' => $e->getMessage(),
                    'class' => get_class($e),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'Exceção durante comunicação com a API CNPJa: ' . $e->getMessage()
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Controller: Erro inesperado na consulta CNPJa', [
                'message' => $e->getMessage(),
                'class' => get_class($e),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erro ao consultar CNPJ: ' . $e->getMessage()
            ], 500);
        }
    }
}
