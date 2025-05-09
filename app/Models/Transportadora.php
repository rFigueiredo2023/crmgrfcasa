<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transportadora extends Model
{
    use HasFactory;

    protected $fillable = [
        'razao_social',
        'cnpj',
        'inscricao_estadual',
        'endereco',
        'codigo_ibge',
        'telefone',
        'celular',
        'contato',
        'email',
        'observacoes',
        'user_id'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
