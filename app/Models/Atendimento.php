<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Atendimento extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'vendedor_id',
        'data_atendimento',
        'tipo_atendimento',
        'descricao',
        'status'
    ];

    protected $casts = [
        'data_atendimento' => 'datetime'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'vendedor_id');
    }
} 