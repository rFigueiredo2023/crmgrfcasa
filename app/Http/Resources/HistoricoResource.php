<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class HistoricoResource extends JsonResource
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
            'tipo' => $this->tipo,
            'texto' => $this->texto,
            'proxima_acao' => $this->proxima_acao,
            'data_proxima_acao' => $this->data_proxima_acao?->format('Y-m-d H:i:s'),
            'retorno' => $this->retorno,
            'data_retorno' => $this->data_retorno?->format('Y-m-d H:i:s'),
            'ativar_lembrete' => $this->ativar_lembrete,
            'data' => $this->data?->format('Y-m-d H:i:s'),
            'anexo' => $this->when($this->anexo, function () {
                return [
                    'url' => Storage::url($this->anexo),
                    'filename' => basename($this->anexo)
                ];
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'usuario' => $this->whenLoaded('usuario', function () {
                return [
                    'id' => $this->usuario->id,
                    'name' => $this->usuario->name,
                ];
            }),
            'historicable_type' => $this->historicable_type,
            'historicable_id' => $this->historicable_id,
        ];
    }
}
