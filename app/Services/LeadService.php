<?php

namespace App\Services;

use App\Enums\StatusLead;
use App\Models\Cliente;
use App\Models\Lead;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class LeadService
{
    /**
     * Cria um novo lead com os dados validados
     */
    public function create(array $validated): Lead
    {
        return Lead::create($validated);
    }

    /**
     * Atualiza um lead existente
     */
    public function update(Lead $lead, array $validated): Lead
    {
        $lead->update($validated);
        return $lead;
    }

    /**
     * Cria um histórico para o lead
     */
    public function adicionarHistorico(Lead $lead, array $validated): \App\Models\Historico
    {
        // Criar o histórico usando o relacionamento polimórfico
        $historico = $lead->historicos()->create([
            'user_id' => auth()->id(),
            'tipo' => $validated['tipo_contato'],
            'texto' => $validated['texto'],
            'proxima_acao' => $validated['proxima_acao'] ?? null,
            'data_proxima_acao' => $validated['data_proxima_acao'] ?? null,
            'retorno' => $validated['retorno'] ?? null,
            'data_retorno' => $validated['data_retorno'] ?? null,
            'ativar_lembrete' => $validated['ativar_lembrete'] ?? false,
            'data' => now()
        ]);

        // Se tiver anexo, salvar
        if (isset($validated['anexo'])) {
            $path = $validated['anexo']->store('atendimentos/anexos', 'public');
            $historico->anexo = $path;
            $historico->save();
        }

        // Atualizar status do lead se fornecido
        if (isset($validated['status']) && $validated['status']) {
            $lead->status = $validated['status'];
            $lead->save();
        }

        return $historico;
    }

    /**
     * Converte um lead em cliente
     */
    public function converterParaCliente(Lead $lead): Cliente
    {
        try {
            DB::beginTransaction();

            // Criar o cliente
            $cliente = Cliente::create([
                'razao_social' => $lead->razao_social,
                'cnpj' => $lead->cnpj,
                'inscricao_estadual' => $lead->inscricao_estadual,
                'endereco' => $lead->endereco,
                'codigo_ibge' => $lead->codigo_ibge,
                'telefone' => $lead->telefone,
                'contato' => $lead->contato,
                'email' => $lead->email,
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
                ]);
            }

            // Mudar status do lead para convertido
            $lead->status = StatusLead::CONVERTIDO->value;
            $lead->save();

            // Deletar o lead e seus anexos em uma versão futura, ou manter para histórico
            // Por enquanto, não vamos excluir, apenas mudar o status

            DB::commit();

            return $cliente;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao converter lead em cliente: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Busca todos os leads com seus relacionamentos
     */
    public function buscarTodos(): Collection
    {
        return Lead::with(['vendedor', 'ultimoHistorico'])->get();
    }

    /**
     * Busca leads agendados para hoje
     */
    public function buscarAcaoHoje(): Collection
    {
        return Lead::acaoHoje()->with(['vendedor', 'ultimoHistorico'])->get();
    }

    /**
     * Busca leads com retorno atrasado
     */
    public function buscarRetornoAtrasado(): Collection
    {
        return Lead::retornoAtrasado()->with(['vendedor', 'ultimoHistorico'])->get();
    }

    /**
     * Busca leads por status
     */
    public function buscarPorStatus(StatusLead $status): Collection
    {
        return Lead::porStatus($status)->with(['vendedor', 'ultimoHistorico'])->get();
    }

    /**
     * Busca leads por vendedor
     */
    public function buscarPorVendedor(int $vendedorId): Collection
    {
        return Lead::porVendedor($vendedorId)->with(['vendedor', 'ultimoHistorico'])->get();
    }

    /**
     * Exclui um lead
     */
    public function excluir(Lead $lead): void
    {
        // Exclui anexos do lead
        foreach ($lead->historicos as $historico) {
            if ($historico->anexo) {
                $path = str_replace('public/', '', $historico->anexo);
                Storage::disk('public')->delete($path);
            }
        }

        // Exclui o lead
        $lead->delete();
    }
}
