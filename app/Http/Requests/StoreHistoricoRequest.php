<?php

namespace App\Http\Requests;

use App\Enums\TipoContato;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreHistoricoRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer esta requisição
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação para a requisição
     */
    public function rules(): array
    {
        return [
            'tipo_contato' => ['required', new Enum(TipoContato::class)],
            'texto' => 'required|string',
            'proxima_acao' => 'nullable|string',
            'data_proxima_acao' => 'nullable|date',
            'retorno' => 'nullable|string',
            'data_retorno' => 'nullable|date',
            'ativar_lembrete' => 'nullable|boolean',
            'anexo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:5120'
        ];
    }

    /**
     * Mensagens de erro personalizadas
     */
    public function messages(): array
    {
        return [
            'tipo_contato.required' => 'O tipo de contato é obrigatório',
            'texto.required' => 'A descrição do histórico é obrigatória',
            'data_proxima_acao.date' => 'A data da próxima ação deve ser uma data válida',
            'data_retorno.date' => 'A data de retorno deve ser uma data válida',
            'anexo.mimes' => 'O anexo deve ser um arquivo dos tipos: pdf, jpg, jpeg, png, gif',
            'anexo.max' => 'O anexo não pode ser maior que 5MB'
        ];
    }

    /**
     * Configurar dados validados antes de processar
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('ativar_lembrete')) {
            $this->merge([
                'ativar_lembrete' => filter_var($this->ativar_lembrete, FILTER_VALIDATE_BOOLEAN)
            ]);
        }
    }
}
