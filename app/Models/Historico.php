<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historico extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'user_id',
        'data',
        'tipo',
        'texto',
        'proxima_acao',
        'data_proxima_acao',
        'anexo'
    ];

    protected $casts = [
        'data' => 'datetime',
        'data_proxima_acao' => 'date'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
} 