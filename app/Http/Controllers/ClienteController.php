<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::with('vendedor')->get();
        return view('content.pages.customers.pages-customers', compact('clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'razao_social' => 'required|string|max:255',
            'cnpj' => 'required|string|max:18|unique:clientes,cnpj',
            'ie' => 'nullable|string|max:20',
            'endereco' => 'required|string|max:255',
            'codigo_ibge' => 'required|string|max:10',
            'telefone' => 'required|string|max:20',
            'contato' => 'required|string|max:255'
        ]);

        $cliente = Cliente::create([
            'razao_social' => $request->razao_social,
            'cnpj' => $request->cnpj,
            'ie' => $request->ie,
            'endereco' => $request->endereco,
            'codigo_ibge' => $request->codigo_ibge,
            'telefone' => $request->telefone,
            'contato' => $request->contato,
            'user_id' => auth()->id() // Registra o usuário que está cadastrando
        ]);

        return redirect()->back()->with('success', 'Cliente cadastrado com sucesso!');
    }
}
