<?php

namespace App\Http\Requests;

use App\Enums\StatusLead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreLeadRequest extends FormRequest
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
            'razao_social' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:18|unique:leads,cnpj',
            'inscricao_estadual' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'codigo_ibge' => 'nullable|string|max:7',
            'telefone' => 'required|string|max:20',
            'contato' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'status' => ['nullable', new Enum(StatusLead::class)],
            'data_proxima_acao' => 'nullable|date',
            'data_retorno' => 'nullable|date',
            'ativar_lembrete' => 'nullable|boolean',
            'user_id' => 'nullable|exists:users,id'
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
            'cnpj.unique' => 'Este CNPJ já está cadastrado como lead',
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

        // Se não for informado o status, define como NOVO
        if (!$this->has('status')) {
            $this->merge([
                'status' => StatusLead::NOVO->value
            ]);
        }

        // Se o usuário não for informado, define como o usuário atual
        if (!$this->has('user_id')) {
            $this->merge([
                'user_id' => auth()->id()
            ]);
        }
    }
}
