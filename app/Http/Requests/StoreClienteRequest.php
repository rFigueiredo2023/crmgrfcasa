<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClienteRequest extends FormRequest
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
            'cnpj' => 'required|string|max:18|unique:clientes,cnpj',
            'inscricao_estadual' => 'nullable|string|max:20',
            'endereco' => 'required|string|max:255',
            'codigo_ibge' => 'required|string|max:10',
            'telefone' => 'required|string|max:20',
            'telefone2' => 'nullable|string|max:20',
            'site' => 'nullable|url|max:255',
            'contato' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'cep' => 'nullable|string|max:10',
            'municipio' => 'required|string|max:100',
            'uf' => 'required|string|max:2',
            'segmento' => 'nullable|string|max:100',
            'segmento_id' => 'nullable|exists:segmentos,id',
            'atividade_principal' => 'nullable|string|max:255',
            'atividades_secundarias' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id'
        ];
    }

    /**
     * Mensagens de erro personalizadas
     */
    public function messages(): array
    {
        return [
            'razao_social.required' => 'A razão social é obrigatória',
            'cnpj.required' => 'O CNPJ é obrigatório',
            'cnpj.unique' => 'Este CNPJ já está cadastrado',
            'endereco.required' => 'O endereço é obrigatório',
            'codigo_ibge.required' => 'O código IBGE é obrigatório',
            'telefone.required' => 'O telefone é obrigatório',
            'contato.required' => 'O contato é obrigatório',
            'municipio.required' => 'O município é obrigatório',
            'uf.required' => 'O estado (UF) é obrigatório',
            'site.url' => 'O site informado não é uma URL válida',
            'segmento_id.exists' => 'O segmento selecionado não existe',
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

        // Se o usuário não for informado, define como o usuário atual
        if (!$this->has('user_id')) {
            $this->merge([
                'user_id' => auth()->id()
            ]);
        }
    }
}
