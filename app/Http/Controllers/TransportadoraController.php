<?php

namespace App\Http\Controllers;

use App\Models\Transportadora;
use Illuminate\Http\Request;

class TransportadoraController extends Controller
{
    public function index()
    {
        $transportadoras = Transportadora::with('usuario')->get();
        return view('content.pages.customers.pages-customers', compact('transportadoras'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'razao_social' => 'required|string|max:255',
            'cnpj' => 'required|string|max:18|unique:transportadoras,cnpj',
            'ie' => 'nullable|string|max:20',
            'endereco' => 'required|string|max:255',
            'codigo_ibge' => 'required|string|max:10',
            'telefone' => 'required|string|max:20',
            'celular' => 'nullable|string|max:20',
            'contato' => 'required|string|max:255',
            'email' => 'required|email|max:255'
        ]);

        $transportadora = Transportadora::create([
            'razao_social' => $request->razao_social,
            'cnpj' => $request->cnpj,
            'ie' => $request->ie,
            'endereco' => $request->endereco,
            'codigo_ibge' => $request->codigo_ibge,
            'telefone' => $request->telefone,
            'celular' => $request->celular,
            'contato' => $request->contato,
            'email' => $request->email,
            'user_id' => auth()->id() // Registra o usuário que está cadastrando
        ]);

        return redirect()->back()->with('success', 'Transportadora cadastrada com sucesso!');
    }
}
