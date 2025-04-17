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
        'proxima_acao',
        'data_proxima_acao',
        'retorno',
        'data_retorno',
        'ativar_lembrete',
        'anexo'
    ];

    protected $casts = [
        'data' => 'datetime',
        'data_proxima_acao' => 'datetime',
        'data_retorno' => 'datetime',
        'ativar_lembrete' => 'boolean'
    ];

    public function historicable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
