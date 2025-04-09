<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Veiculo extends Model
{
    use HasFactory;

    protected $fillable = [
        'motorista',
        'marca',
        'modelo',
        'ano_fabricacao',
        'mes_licenca',
        'local',
        'placa',
        'uf',
        'tara',
        'capacidade_kg',
        'capacidade_m3',
        'tipo_rodagem',
        'tipo_carroceria',
        'renavam',
        'cpf_cnpj_proprietario',
        'proprietario',
        'uf_proprietario',
        'tipo_proprietario',
        'detalhes',
        'user_id'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
