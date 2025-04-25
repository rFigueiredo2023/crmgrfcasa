<?php

namespace App\Services;

use App\Models\Cliente;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ClienteService
{
    /**
     * Cria um novo cliente com os dados validados
     */
    public function create(array $validated): Cliente
    {
        return Cliente::create($validated);
    }

    /**
     * Atualiza um cliente existente
     */
    public function update(Cliente $cliente, array $validated): Cliente
    {
        $cliente->update($validated);
        return $cliente;
    }

    /**
     * Cria um histórico para o cliente
     */
    public function adicionarHistorico(Cliente $cliente, array $validated): \App\Models\Historico
    {
        try {
            DB::beginTransaction();

            // Criar o histórico usando o relacionamento polimórfico
            $historico = $cliente->historicos()->create([
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
            if (isset($validated['anexo']) && $validated['anexo'] instanceof UploadedFile) {
                $path = $validated['anexo']->store('atendimentos/anexos', 'public');
                $historico->anexo = $path;
                $historico->save();
            }

            DB::commit();
            return $historico;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erro ao adicionar histórico para cliente: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Busca todos os clientes com seus relacionamentos
     */
    public function buscarTodos(): Collection
    {
        return Cliente::with(['vendedor', 'ultimoHistorico', 'segmento'])->get();
    }

    /**
     * Busca clientes de um vendedor específico
     */
    public function buscarPorVendedor(int $vendedorId): Collection
    {
        return Cliente::porVendedor($vendedorId)->with(['vendedor', 'ultimoHistorico', 'segmento'])->get();
    }

    /**
     * Busca clientes por segmento
     */
    public function buscarPorSegmento(int $segmentoId): Collection
    {
        return Cliente::porSegmento($segmentoId)->with(['vendedor', 'ultimoHistorico', 'segmento'])->get();
    }

    /**
     * Busca clientes por estado (UF)
     */
    public function buscarPorEstado(string $uf): Collection
    {
        return Cliente::porEstado($uf)->with(['vendedor', 'ultimoHistorico', 'segmento'])->get();
    }

    /**
     * Busca clientes com atendimentos recentes
     */
    public function buscarComAtendimentoRecente(int $diasAtras = 30): Collection
    {
        return Cliente::comAtendimentoRecente($diasAtras)->with(['vendedor', 'ultimoHistorico', 'segmento'])->get();
    }

    /**
     * Busca clientes sem atendimentos recentes
     */
    public function buscarSemAtendimentoRecente(int $diasAtras = 30): Collection
    {
        return Cliente::semAtendimentoRecente($diasAtras)->with(['vendedor', 'ultimoHistorico', 'segmento'])->get();
    }

    /**
     * Exclui um cliente (soft delete)
     */
    public function excluir(Cliente $cliente): void
    {
        $cliente->delete();
    }

    /**
     * Consulta CNPJ na API externa
     */
    public function consultarCnpj(string $cnpj): array
    {
        try {
            // Obtém o CNPJ da requisição e remove caracteres não numéricos
            $cnpj = preg_replace('/\D/', '', $cnpj);

            // Verifica se o CNPJ tem 14 dígitos
            if (strlen($cnpj) !== 14) {
                throw new Exception('CNPJ inválido. Deve conter 14 dígitos.');
            }

            Log::info('Service: Iniciando consulta CNPJa', ['cnpj' => $cnpj]);

            // Token de autenticação da API CNPJa
            $apiToken = trim(config('services.cnpja.token'));
            $baseUrl = trim(config('services.cnpja.base_url', 'https://api.cnpja.com'));

            Log::info('Service: Configuração CNPJa', [
                'token_length' => strlen($apiToken),
                'token_first_chars' => substr($apiToken, 0, 5) . '...',
                'base_url' => $baseUrl
            ]);

            // Requisição para a API CNPJa
            $url = "{$baseUrl}/office/{$cnpj}?registrations=BR&suframa=true";
            $response = Http::withHeaders([
                'Authorization' => $apiToken
            ])->get($url);

            // Log da resposta HTTP
            Log::info('Service: Resposta HTTP da API CNPJa', [
                'status' => $response->status(),
                'successful' => $response->successful()
            ]);

            // Se a resposta contiver erro
            if (!$response->successful()) {
                Log::error('Service: Erro na resposta CNPJa', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                // Mensagem personalizada para erro de autenticação
                if ($response->status() === 401) {
                    throw new Exception('Erro de autenticação na API CNPJa. O token de acesso pode estar inválido ou expirado.');
                }

                throw new Exception('Erro ao consultar CNPJ: HTTP ' . $response->status() . ' - ' . ($response->json()['message'] ?? $response->body()));
            }

            // Log de sucesso
            $data = $response->json();
            Log::info('Service: Consulta CNPJa bem-sucedida', [
                'razao_social' => $data['company']['name'] ?? 'N/A',
                'situacao' => $data['status']['text'] ?? 'N/A'
            ]);

            return $data;
        } catch (Exception $e) {
            Log::error('Service: Erro na consulta CNPJa', [
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}
