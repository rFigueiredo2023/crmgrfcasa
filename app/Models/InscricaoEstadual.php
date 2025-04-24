<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InscricaoEstadual extends Model
{
    use HasFactory;

    /**
     * A tabela associada ao modelo.
     *
     * @var string
     */
    protected $table = 'inscricoes_estaduais';

    /**
     * Os atributos que são atribuíveis em massa.
     *
     * @var array
     */
    protected $fillable = [
        'cliente_id',
        'estado',
        'numero_ie',
        'tipo_ie',
        'status_ie',
        'data_status_ie'
    ];

    /**
     * Os atributos que devem ser convertidos para tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'data_status_ie' => 'date',
    ];

    /**
     * Obter o cliente que possui esta inscrição estadual.
     */
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}
