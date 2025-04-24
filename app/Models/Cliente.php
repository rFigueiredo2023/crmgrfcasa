<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cliente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'razao_social',
        'cnpj',
        'inscricao_estadual',
        'endereco',
        'codigo_ibge',
        'telefone',
        'telefone2',
        'site',
        'contato',
        'email',
        'cep',
        'municipio',
        'uf',
        'segmento',
        'segmento_id',
        'user_id',
        'atividade_principal',
        'atividades_secundarias',
        // Novos campos CNPJa
        'nome_fantasia',
        'fundacao',
        'situacao',
        'data_situacao',
        'natureza_juridica',
        'porte',
        'capital_social',
        'simples_nacional',
        'logradouro',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'complemento',
        'dominio_email',
        'cnae_principal',
        'cnaes_secundarios',
        'socio_principal',
        'funcao_socio',
        'idade_socio',
        'lista_socios',
        'suframa',
        'status_suframa',
        'tipo_contribuinte',
        'regime_tributario'
    ];

    protected $casts = [
        'fundacao' => 'date',
        'data_situacao' => 'date',
        'capital_social' => 'decimal:2',
        'simples_nacional' => 'boolean',
        'cnaes_secundarios' => 'array',
        'lista_socios' => 'array',
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
        return $this->morphMany(Historico::class, 'historicable');
    }

    public function ultimoHistorico()
    {
        return $this->morphOne(Historico::class, 'historicable')->latest('data');
    }

    // Relacionamento com Segmento
    public function segmento()
    {
        return $this->belongsTo(Segmento::class);
    }

    // Relacionamento com Inscrições Estaduais
    public function inscricoesEstaduais()
    {
        return $this->hasMany(InscricaoEstadual::class);
    }
}
