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

    public function atendimentos()
    {
        return $this->hasMany(Atendimento::class);
    }

    public function arquivos()
    {
        return $this->hasMany(Arquivo::class);
    }

    public function mensagens()
    {
        return $this->hasMany(Mensagem::class);
    }

    public function ultimoAtendimento()
    {
        return $this->hasOne(Atendimento::class)->latest();
    }

    public function historicos()
    {
        return $this->morphMany(Historico::class, 'historicoable');
    }
}
