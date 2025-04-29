<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atendimento extends Model
{
    use HasFactory;

    protected $fillable = [
        'atendivel_id',
        'atendivel_type',
        'user_id',
        'tipo_contato',
        'descricao',
        'retorno',
        'data_retorno',
        'proxima_acao',
        'data_proxima_acao',
        'ativar_lembrete',
        'anexo',
        'status',
        'data_atendimento'
    ];

    protected $casts = [
        'data_retorno' => 'datetime',
        'data_proxima_acao' => 'datetime',
        'data_atendimento' => 'datetime',
        'ativar_lembrete' => 'boolean'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($atendimento) {
            if (!$atendimento->data_atendimento) {
                $atendimento->data_atendimento = now();
            }
        });
    }

    public function atendivel()
    {
        return $this->morphTo();
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'atendivel_id')->where('atendivel_type', Cliente::class);
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
