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
        'chassi',
        'km_oleo',
        'km_correia',
        'segurado_ate',
        'limite_km_mes',
        'tara',
        'capacidade_kg',
        'capacidade_m3',
        'tipo_rodagem',
        'tipo_carroceria',
        'renavam',
        'responsavel_atual',
        'cpf_cnpj_proprietario',
        'proprietario',
        'antt_rntrc',
        'uf_proprietario',
        'ie_proprietario',
        'tipo_proprietario',
        'detalhes',
        'user_id'
    ];

    protected $casts = [
        'segurado_ate' => 'date',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
