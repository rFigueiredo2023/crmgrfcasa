<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mensagem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cliente_id',
        'user_id',
        'conteudo',
        'lida',
        'lida_em'
    ];

    protected $casts = [
        'lida' => 'boolean',
        'lida_em' => 'datetime'
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