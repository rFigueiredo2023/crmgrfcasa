<?php

namespace App\Http\Controllers;

use App\Models\Veiculo;
use Illuminate\Http\Request;

class VeiculoController extends Controller
{
    public function index()
    {
        $veiculos = Veiculo::with('usuario')->get();
        return view('content.pages.customers.pages-customers', compact('veiculos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'motorista' => 'required|string|max:255',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'ano_fabricacao' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mes_licenca' => 'required|string|max:20',
            'local' => 'required|string|max:255',
            'placa' => 'required|string|max:8|unique:veiculos,placa',
            'uf' => 'required|string|size:2',
            'tara' => 'required|numeric|min:0',
            'capacidade_kg' => 'required|numeric|min:0',
            'capacidade_m3' => 'required|numeric|min:0',
            'tipo_rodagem' => 'required|in:truck,toco,cavalo_mecanico,van,utilitarios,outros',
            'tipo_carroceria' => 'required|in:aberta,bau,outros,slider',
            'renavam' => 'required|string|max:20',
            'cpf_cnpj_proprietario' => 'required|string|max:20',
            'proprietario' => 'required|string|max:255',
            'uf_proprietario' => 'required|string|size:2',
            'tipo_proprietario' => 'required|string|max:50',
            'detalhes' => 'nullable|string'
        ]);

        $veiculo = Veiculo::create([
            'motorista' => $request->motorista,
            'marca' => $request->marca,
            'modelo' => $request->modelo,
            'ano_fabricacao' => $request->ano_fabricacao,
            'mes_licenca' => $request->mes_licenca,
            'local' => $request->local,
            'placa' => $request->placa,
            'uf' => $request->uf,
            'tara' => $request->tara,
            'capacidade_kg' => $request->capacidade_kg,
            'capacidade_m3' => $request->capacidade_m3,
            'tipo_rodagem' => $request->tipo_rodagem,
            'tipo_carroceria' => $request->tipo_carroceria,
            'renavam' => $request->renavam,
            'cpf_cnpj_proprietario' => $request->cpf_cnpj_proprietario,
            'proprietario' => $request->proprietario,
            'uf_proprietario' => $request->uf_proprietario,
            'tipo_proprietario' => $request->tipo_proprietario,
            'detalhes' => $request->detalhes,
            'user_id' => auth()->id() // Registra o usuário que está cadastrando
        ]);

        return redirect()->back()->with('success', 'Veículo cadastrado com sucesso!');
    }
}
