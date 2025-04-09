<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $fillable = [
        'razao_social',
        'cnpj',
        'ie',
        'endereco',
        'codigo_ibge',
        'telefone',
        'contato',
        'user_id'
    ];

    public function vendedor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
