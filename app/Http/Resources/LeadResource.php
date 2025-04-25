<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
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
            'cnpj' => $this->cnpj,
            'inscricao_estadual' => $this->inscricao_estadual,
            'endereco' => $this->endereco,
            'codigo_ibge' => $this->codigo_ibge,
            'telefone' => $this->telefone,
            'contato' => $this->contato,
            'email' => $this->email,
            'status' => $this->status?->value,
            'status_label' => $this->status?->label(),
            'status_color' => $this->status?->color(),
            'data_proxima_acao' => $this->data_proxima_acao?->format('Y-m-d H:i:s'),
            'data_retorno' => $this->data_retorno?->format('Y-m-d H:i:s'),
            'ativar_lembrete' => $this->ativar_lembrete,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'vendedor' => $this->whenLoaded('vendedor', function () {
                return [
                    'id' => $this->vendedor->id,
                    'name' => $this->vendedor->name,
                ];
            }),
            'historicos' => HistoricoResource::collection($this->whenLoaded('historicos')),
            'ultimo_historico' => new HistoricoResource($this->whenLoaded('ultimoHistorico')),
        ];
    }
}
