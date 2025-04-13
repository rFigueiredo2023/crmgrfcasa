<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historico extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'data',
        'tipo',
        'texto',
        'retorno',
        'data_retorno',
        'ativar_lembrete',
        'proxima_acao',
        'data_proxima_acao',
        'anexo'
    ];

    protected $casts = [
        'data' => 'datetime',
        'data_retorno' => 'datetime',
        'data_proxima_acao' => 'date',
        'ativar_lembrete' => 'boolean'
    ];

    public function historicoable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 