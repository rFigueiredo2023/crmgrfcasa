<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Transportadora;
use App\Models\Veiculo;

class Custormers extends Controller
{
    public function index()
    {
        $clientes = Cliente::with('vendedor')->get();
        $transportadoras = Transportadora::with('usuario')->get();
        $veiculos = Veiculo::with('usuario')->get();

        return view('content.pages.customers.pages-customers', compact('clientes', 'transportadoras', 'veiculos'));
    }
}
