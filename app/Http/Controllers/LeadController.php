<?php

namespace App\Http\Controllers;

use App\Enums\StatusLead;
use App\Http\Requests\StoreHistoricoRequest;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\UpdateLeadRequest;
use App\Http\Resources\LeadResource;
use App\Models\Lead;
use App\Services\LeadService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class LeadController extends Controller
{
    /**
     * @var LeadService
     */
    protected LeadService $leadService;

    /**
     * Construtor com injeção do serviço de leads
     */
    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
    }

    /**
     * Exibe a página de listagem de leads
     */
    public function index(): View
    {
        $leads = $this->leadService->buscarTodos();
        return view('content.pages.leads.pages-leads', compact('leads'));
    }

    /**
     * Salva um novo lead
     */
    public function store(StoreLeadRequest $request): RedirectResponse
    {
        try {
            $this->leadService->create($request->validated());
            return redirect()->back()->with('success', 'Lead cadastrado com sucesso!');
        } catch (Exception $e) {
            Log::error('Erro ao criar lead: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao cadastrar lead: ' . $e->getMessage());
        }
    }

    /**
     * Atualiza um lead existente
     */
    public function update(UpdateLeadRequest $request, Lead $lead): RedirectResponse
    {
        try {
            $this->leadService->update($lead, $request->validated());
            return redirect()->back()->with('success', 'Lead atualizado com sucesso!');
        } catch (Exception $e) {
            Log::error('Erro ao atualizar lead: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao atualizar lead: ' . $e->getMessage());
        }
    }

    /**
     * Remove um lead
     */
    public function destroy(Lead $lead): RedirectResponse
    {
        try {
            $this->leadService->excluir($lead);
            return redirect()->back()->with('success', 'Lead excluído com sucesso!');
        } catch (Exception $e) {
            Log::error('Erro ao excluir lead: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erro ao excluir lead: ' . $e->getMessage());
        }
    }

    /**
     * Mostra o histórico de um lead
     */
    public function historico(Lead $lead): JsonResponse
    {
        try {
            $lead->load(['historicos.usuario', 'vendedor']);

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
                'historicos' => $lead->historicos->map(function($historico) {
                    return [
                        'id' => $historico->id,
                        'tipo' => $historico->tipo,
                        'data' => $historico->data->format('d/m/Y H:i'),
                        'texto' => $historico->texto,
                        'vendedora' => $historico->usuario ? $historico->usuario->name : 'Não atribuído',
                        'status' => 'em_andamento',
                        'retorno' => $historico->retorno,
                        'data_retorno' => $historico->data_retorno ? $historico->data_retorno->format('d/m/Y \à\s H:i') : null,
                        'proxima_acao' => $historico->proxima_acao,
                        'data_proxima_acao' => $historico->data_proxima_acao ? $historico->data_proxima_acao->format('d/m/Y \à\s H:i') : null,
                        'ativar_lembrete' => $historico->ativar_lembrete,
                    ];
                }),
                'message' => 'Históricos carregados com sucesso'
            ]);
        } catch (Exception $e) {
            Log::error('Erro ao carregar histórico de lead: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'message' => 'Ocorreu um erro ao processar o histórico de lead.'
            ], 500);
        }
    }

    /**
     * Salva um novo histórico para o lead
     */
    public function storeHistorico(StoreHistoricoRequest $request, Lead $lead): JsonResponse
    {
        try {
            $historico = $this->leadService->adicionarHistorico($lead, $request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Atendimento registrado com sucesso!',
                'historico' => $historico
            ]);
        } catch (Exception $e) {
            Log::error('Erro ao registrar histórico de lead: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar atendimento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Converte um lead em cliente
     */
    public function converter(Lead $lead): JsonResponse
    {
        try {
            $cliente = $this->leadService->converterParaCliente($lead);

            return response()->json([
                'success' => true,
                'message' => 'Lead convertido em cliente com sucesso!',
                'cliente_id' => $cliente->id
            ]);
        } catch (Exception $e) {
            Log::error('Erro ao converter lead em cliente: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao converter lead em cliente: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retorna dados de um lead para API
     */
    public function show(Lead $lead): LeadResource
    {
        $lead->load(['vendedor', 'historicos']);
        return new LeadResource($lead);
    }

    /**
     * Criar um novo lead com atendimento inicial
     */
    public function storeComAtendimento(StoreLeadRequest $request): JsonResponse
    {
        try {
            // Criar o lead
            $lead = $this->leadService->create($request->validated());

            // Criar um histórico para esse lead
            $historico = $this->leadService->adicionarHistorico($lead, [
                'tipo_contato' => $request->tipo_contato,
                'texto' => $request->descricao,
                'proxima_acao' => $request->proxima_acao,
                'data_proxima_acao' => $request->data_proxima_acao,
                'retorno' => $request->retorno,
                'data_retorno' => $request->data_retorno,
                'ativar_lembrete' => $request->ativar_lembrete,
                'anexo' => $request->file('anexo')
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Lead e atendimento registrados com sucesso!',
                'lead' => new LeadResource($lead),
                'historico' => $historico
            ]);
        } catch (Exception $e) {
            Log::error('Erro ao registrar lead com atendimento: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar lead e atendimento: ' . $e->getMessage()
            ], 500);
        }
    }
}
