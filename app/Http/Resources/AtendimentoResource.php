<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AtendimentoResource extends JsonResource
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
            'tipo_contato' => $this->tipo_contato,
            'status' => $this->status,
            'descricao' => $this->descricao,
            'retorno' => $this->retorno,
            'data_retorno' => $this->data_retorno?->format('Y-m-d H:i:s'),
            'proxima_acao' => $this->proxima_acao,
            'data_proxima_acao' => $this->data_proxima_acao?->format('Y-m-d H:i:s'),
            'data_atendimento' => $this->data_atendimento?->format('Y-m-d H:i:s'),
            'anexo' => $this->when($this->anexo, function () {
                return [
                    'url' => Storage::url($this->anexo),
                    'filename' => basename($this->anexo)
                ];
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'cliente_id' => $this->cliente_id,
            'user_id' => $this->user_id,
            'vendedor' => $this->whenLoaded('vendedor', function () {
                return [
                    'id' => $this->vendedor->id,
                    'name' => $this->vendedor->name,
                ];
            }),
            'cliente' => $this->whenLoaded('cliente', function () {
                return [
                    'id' => $this->cliente->id,
                    'razao_social' => $this->cliente->razao_social,
                    'cnpj' => $this->cliente->cnpj,
                ];
            }),
        ];
    }
}
