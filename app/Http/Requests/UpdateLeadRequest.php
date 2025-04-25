<?php

namespace App\Http\Requests;

use App\Enums\StatusLead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateLeadRequest extends FormRequest
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
            'razao_social' => 'sometimes|required|string|max:255',
            'cnpj' => 'sometimes|nullable|string|max:18|unique:leads,cnpj,' . $this->lead->id,
            'inscricao_estadual' => 'sometimes|nullable|string|max:20',
            'endereco' => 'sometimes|nullable|string|max:255',
            'codigo_ibge' => 'sometimes|nullable|string|max:7',
            'telefone' => 'sometimes|required|string|max:20',
            'contato' => 'sometimes|nullable|string|max:255',
            'email' => 'sometimes|nullable|email|max:255',
            'status' => ['sometimes', 'nullable', new Enum(StatusLead::class)],
            'data_proxima_acao' => 'sometimes|nullable|date',
            'data_retorno' => 'sometimes|nullable|date',
            'ativar_lembrete' => 'sometimes|nullable|boolean',
            'user_id' => 'sometimes|nullable|exists:users,id'
        ];
    }

    /**
     * Mensagens de erro personalizadas
     */
    public function messages(): array
    {
        return [
            'razao_social.required' => 'O nome da empresa é obrigatório',
            'razao_social.max' => 'O nome da empresa não pode ter mais de 255 caracteres',
            'cnpj.unique' => 'Este CNPJ já está cadastrado para outro lead',
            'telefone.required' => 'O telefone é obrigatório',
            'email.email' => 'O e-mail informado não é válido',
            'user_id.exists' => 'O vendedor selecionado não existe'
        ];
    }

    /**
     * Configurar dados validados antes de processar
     */
    protected function prepareForValidation(): void
    {
        // Formata o CNPJ (removendo caracteres não numéricos)
        if ($this->has('cnpj')) {
            $this->merge([
                'cnpj' => preg_replace('/\D/', '', $this->cnpj)
            ]);
        }
    }
}
