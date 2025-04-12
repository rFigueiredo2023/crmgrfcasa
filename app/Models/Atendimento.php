<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atendimento extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'user_id',
        'tipo',
        'descricao',
        'retorno',
        'data_retorno',
        'proxima_acao',
        'data_proxima_acao',
        'ativar_lembrete',
        'anexo',
        'status'
    ];

    protected $casts = [
        'data_retorno' => 'datetime',
        'data_proxima_acao' => 'datetime',
        'ativar_lembrete' => 'boolean'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
} 