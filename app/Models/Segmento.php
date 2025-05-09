<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Segmento extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome'
    ];

    public function clientes()
    {
        return $this->hasMany(Cliente::class);
    }
}
