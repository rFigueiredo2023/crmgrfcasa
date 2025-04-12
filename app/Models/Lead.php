<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'telefone',
        'email',
        'origem',
        'status',
        'observacoes',
        'user_id'
    ];

    public function vendedora()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}