<?php

namespace App\Models;

use App\Enums\StatusLead;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Builder;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'razao_social',
        'cnpj',
        'inscricao_estadual',
        'endereco',
        'codigo_ibge',
        'telefone',
        'contato',
        'email',
        'status',
        'data_proxima_acao',
        'data_retorno',
        'ativar_lembrete',
        'user_id'
    ];

    protected $casts = [
        'data_proxima_acao' => 'datetime',
        'data_retorno' => 'datetime',
        'ativar_lembrete' => 'boolean',
        'status' => StatusLead::class,
    ];

    /**
     * Lead pertence a um vendedor
     */
    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Lead possui muitos históricos
     */
    public function historicos(): MorphMany
    {
        return $this->morphMany(Historico::class, 'historicable');
    }

    /**
     * Último histórico do lead
     */
    public function ultimoHistorico(): MorphOne
    {
        return $this->morphOne(Historico::class, 'historicable')->latest('data');
    }

    /**
     * Escopo para filtrar leads por status
     */
    public function scopePorStatus(Builder $query, StatusLead $status): Builder
    {
        return $query->where('status', $status->value);
    }

    /**
     * Escopo para leads que precisam de ação hoje
     */
    public function scopeAcaoHoje(Builder $query): Builder
    {
        return $query->whereDate('data_proxima_acao', now()->toDateString());
    }

    /**
     * Escopo para leads de um vendedor específico
     */
    public function scopePorVendedor(Builder $query, int $vendedorId): Builder
    {
        return $query->where('user_id', $vendedorId);
    }

    /**
     * Escopo para leads com retorno atrasado
     */
    public function scopeRetornoAtrasado(Builder $query): Builder
    {
        return $query->whereNotNull('data_retorno')
            ->whereDate('data_retorno', '<', now()->toDateString());
    }

    /**
     * Verifica se o lead tem ação agendada para hoje
     */
    public function temAcaoHoje(): bool
    {
        return $this->data_proxima_acao?->isToday() ?? false;
    }
}
