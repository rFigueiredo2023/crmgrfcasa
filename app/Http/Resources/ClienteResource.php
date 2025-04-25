<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClienteResource extends JsonResource
{
    /**
     * Transforma o resource em um array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'razao_social' => $this->razao_social,
            'nome_fantasia' => $this->nome_fantasia,
            'cnpj' => $this->cnpj,
            'cnpj_formatado' => $this->cnpjFormatado,
            'inscricao_estadual' => $this->inscricao_estadual,
            'endereco' => $this->endereco,
            'codigo_ibge' => $this->codigo_ibge,
            'telefone' => $this->telefone,
            'telefone2' => $this->telefone2,
            'site' => $this->site,
            'contato' => $this->contato,
            'email' => $this->email,
            'cep' => $this->cep,
            'municipio' => $this->municipio,
            'uf' => $this->uf,
            'segmento' => $this->segmento,
            'segmento_id' => $this->segmento_id,
            'atividade_principal' => $this->atividade_principal,
            'fundacao' => $this->fundacao?->format('Y-m-d'),
            'situacao' => $this->situacao,
            'data_situacao' => $this->data_situacao?->format('Y-m-d'),
            'capital_social' => $this->capital_social,
            'simples_nacional' => $this->simples_nacional,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),
            'vendedor' => $this->whenLoaded('vendedor', function () {
                return [
                    'id' => $this->vendedor->id,
                    'name' => $this->vendedor->name,
                ];
            }),
            'segmento_objeto' => $this->whenLoaded('segmento', function () {
                return [
                    'id' => $this->segmento->id,
                    'nome' => $this->segmento->nome,
                ];
            }),
            'atendimentos' => AtendimentoResource::collection($this->whenLoaded('atendimentos')),
            'ultimo_atendimento' => new AtendimentoResource($this->whenLoaded('ultimoAtendimento')),
            'historicos' => HistoricoResource::collection($this->whenLoaded('historicos')),
            'ultimo_historico' => new HistoricoResource($this->whenLoaded('ultimoHistorico')),
            'arquivos' => ArquivoResource::collection($this->whenLoaded('arquivos')),
            'mensagens' => MensagemResource::collection($this->whenLoaded('mensagens')),
            'inscricoes_estaduais' => InscricaoEstadualResource::collection($this->whenLoaded('inscricoesEstaduais')),
        ];
    }
}
