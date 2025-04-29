<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Builder;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'razao_social',
        'cnpj',
        'inscricao_estadual',
        'endereco',
        'codigo_ibge',
        'telefone',
        'telefone2',
        'site',
        'contato',
        'email',
        'cep',
        'municipio',
        'uf',
        'segmento',
        'segmento_id',
        'user_id',
        'atividade_principal',
        'atividades_secundarias',
        // Campos CNPJa
        'nome_fantasia',
        'fundacao',
        'situacao',
        'data_situacao',
        'natureza_juridica',
        'porte',
        'capital_social',
        'simples_nacional',
        'logradouro',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'complemento',
        'dominio_email',
        'cnae_principal',
        'cnaes_secundarios',
        'socio_principal',
        'funcao_socio',
        'idade_socio',
        'lista_socios',
        'suframa',
        'status_suframa',
        'tipo_contribuinte',
        'regime_tributario'
    ];

    protected $casts = [
        'fundacao' => 'date',
        'data_situacao' => 'date',
        'capital_social' => 'decimal:2',
        'simples_nacional' => 'boolean',
        'cnaes_secundarios' => 'array',
        'lista_socios' => 'array',
    ];

    /**
     * Retorna o usuário vendedor responsável pelo cliente
     */
    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Retorna os atendimentos do cliente
     */
    public function atendimentos(): HasMany
    {
        return $this->morphMany(Atendimento::class, 'atendivel');
    }

    /**
     * Retorna os arquivos do cliente
     */
    public function arquivos(): HasMany
    {
        return $this->hasMany(Arquivo::class);
    }

    /**
     * Retorna as mensagens do cliente
     */
    public function mensagens(): HasMany
    {
        return $this->hasMany(Mensagem::class);
    }

    /**
     * Retorna o último atendimento do cliente
     */
    public function ultimoAtendimento(): MorphOne
    {
        return $this->morphOne(Atendimento::class, 'atendivel')->latest();
    }

    /**
     * Retorna os históricos do cliente
     */
    public function historicos(): MorphMany
    {
        return $this->morphMany(Historico::class, 'historicable');
    }

    /**
     * Retorna o último histórico do cliente
     */
    public function ultimoHistorico(): MorphOne
    {
        return $this->morphOne(Historico::class, 'historicable')->latest('data');
    }

    /**
     * Retorna o segmento do cliente
     */
    public function segmento(): BelongsTo
    {
        return $this->belongsTo(Segmento::class);
    }

    /**
     * Retorna as inscrições estaduais do cliente
     */
    public function inscricoesEstaduais(): HasMany
    {
        return $this->hasMany(InscricaoEstadual::class);
    }

    /**
     * Escopo para clientes de um vendedor específico
     */
    public function scopePorVendedor(Builder $query, int $vendedorId): Builder
    {
        return $query->where('user_id', $vendedorId);
    }

    /**
     * Escopo para clientes de um segmento específico
     */
    public function scopePorSegmento(Builder $query, int $segmentoId): Builder
    {
        return $query->where('segmento_id', $segmentoId);
    }

    /**
     * Escopo para clientes por estado (UF)
     */
    public function scopePorEstado(Builder $query, string $uf): Builder
    {
        return $query->where('uf', $uf);
    }

    /**
     * Escopo para clientes com atendimentos recentes
     */
    public function scopeComAtendimentoRecente(Builder $query, int $diasAtras = 30): Builder
    {
        return $query->whereHas('atendimentos', function ($query) use ($diasAtras) {
            $query->where('created_at', '>=', now()->subDays($diasAtras));
        });
    }

    /**
     * Escopo para clientes sem atendimentos recentes
     */
    public function scopeSemAtendimentoRecente(Builder $query, int $diasAtras = 30): Builder
    {
        return $query->whereDoesntHave('atendimentos', function ($query) use ($diasAtras) {
            $query->where('created_at', '>=', now()->subDays($diasAtras));
        });
    }

    /**
     * Formata o CNPJ para exibição
     */
    public function getCnpjFormatadoAttribute(): string
    {
        $cnpj = preg_replace('/\D/', '', $this->cnpj);
        if (strlen($cnpj) !== 14) {
            return $this->cnpj;
        }
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
    }
}
