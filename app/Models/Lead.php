<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'ativar_lembrete' => 'boolean'
    ];

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function historicos()
    {
        return $this->morphMany(Historico::class, 'historicable');
    }

    public function ultimoHistorico()
    {
        return $this->morphOne(Historico::class, 'historicable')->latest('data');
    }
}
