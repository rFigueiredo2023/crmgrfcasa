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
            'chassi' => 'nullable|string|max:25',
            'km_oleo' => 'nullable|integer',
            'km_correia' => 'nullable|integer',
            'segurado_ate' => 'nullable|date',
            'limite_km_mes' => 'nullable|integer',
            'tara' => 'required|numeric|min:0',
            'capacidade_kg' => 'required|numeric|min:0',
            'capacidade_m3' => 'required|numeric|min:0',
            'tipo_rodagem' => 'required|in:truck,toco,cavalo_mecanico,van,utilitarios,outros',
            'tipo_carroceria' => 'required|in:aberta,fechada-bau,granelera,porta-container,slider,outros',
            'renavam' => 'required|string|max:20',
            'responsavel_atual' => 'nullable|string|max:100',
            'cpf_cnpj_proprietario' => 'required|string|max:20',
            'proprietario' => 'required|string|max:255',
            'antt_rntrc' => 'nullable|string|max:50',
            'uf_proprietario' => 'required|string|size:2',
            'ie_proprietario' => 'nullable|string|max:30',
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
            'chassi' => $request->chassi,
            'km_oleo' => $request->km_oleo,
            'km_correia' => $request->km_correia,
            'segurado_ate' => $request->segurado_ate,
            'limite_km_mes' => $request->limite_km_mes,
            'tara' => $request->tara,
            'capacidade_kg' => $request->capacidade_kg,
            'capacidade_m3' => $request->capacidade_m3,
            'tipo_rodagem' => $request->tipo_rodagem,
            'tipo_carroceria' => $request->tipo_carroceria,
            'renavam' => $request->renavam,
            'responsavel_atual' => $request->responsavel_atual,
            'cpf_cnpj_proprietario' => $request->cpf_cnpj_proprietario,
            'proprietario' => $request->proprietario,
            'antt_rntrc' => $request->antt_rntrc,
            'uf_proprietario' => $request->uf_proprietario,
            'ie_proprietario' => $request->ie_proprietario,
            'tipo_proprietario' => $request->tipo_proprietario,
            'detalhes' => $request->detalhes,
            'user_id' => auth()->id()
        ]);

        return redirect()->back()->with('success', 'Veículo cadastrado com sucesso!');
    }

    public function show(Veiculo $veiculo)
    {
        return view('content.pages.customers.veiculo-detail', compact('veiculo'));
    }

    public function edit(Veiculo $veiculo)
    {
        return response()->json($veiculo);
    }

    public function update(Request $request, Veiculo $veiculo)
    {
        $request->validate([
            'motorista' => 'required|string|max:255',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'ano_fabricacao' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mes_licenca' => 'required|string|max:20',
            'local' => 'required|string|max:255',
            'placa' => 'required|string|max:8|unique:veiculos,placa,' . $veiculo->id,
            'uf' => 'required|string|size:2',
            'chassi' => 'nullable|string|max:25',
            'km_oleo' => 'nullable|integer',
            'km_correia' => 'nullable|integer',
            'segurado_ate' => 'nullable|date',
            'limite_km_mes' => 'nullable|integer',
            'tara' => 'required|numeric|min:0',
            'capacidade_kg' => 'required|numeric|min:0',
            'capacidade_m3' => 'required|numeric|min:0',
            'tipo_rodagem' => 'required|in:truck,toco,cavalo_mecanico,van,utilitarios,outros',
            'tipo_carroceria' => 'required|in:aberta,fechada-bau,granelera,porta-container,slider,outros',
            'renavam' => 'required|string|max:20',
            'responsavel_atual' => 'nullable|string|max:100',
            'cpf_cnpj_proprietario' => 'required|string|max:20',
            'proprietario' => 'required|string|max:255',
            'antt_rntrc' => 'nullable|string|max:50',
            'uf_proprietario' => 'required|string|size:2',
            'ie_proprietario' => 'nullable|string|max:30',
            'tipo_proprietario' => 'required|string|max:50',
            'detalhes' => 'nullable|string'
        ]);

        $veiculo->update([
            'motorista' => $request->motorista,
            'marca' => $request->marca,
            'modelo' => $request->modelo,
            'ano_fabricacao' => $request->ano_fabricacao,
            'mes_licenca' => $request->mes_licenca,
            'local' => $request->local,
            'placa' => $request->placa,
            'uf' => $request->uf,
            'chassi' => $request->chassi,
            'km_oleo' => $request->km_oleo,
            'km_correia' => $request->km_correia,
            'segurado_ate' => $request->segurado_ate,
            'limite_km_mes' => $request->limite_km_mes,
            'tara' => $request->tara,
            'capacidade_kg' => $request->capacidade_kg,
            'capacidade_m3' => $request->capacidade_m3,
            'tipo_rodagem' => $request->tipo_rodagem,
            'tipo_carroceria' => $request->tipo_carroceria,
            'renavam' => $request->renavam,
            'responsavel_atual' => $request->responsavel_atual,
            'cpf_cnpj_proprietario' => $request->cpf_cnpj_proprietario,
            'proprietario' => $request->proprietario,
            'antt_rntrc' => $request->antt_rntrc,
            'uf_proprietario' => $request->uf_proprietario,
            'ie_proprietario' => $request->ie_proprietario,
            'tipo_proprietario' => $request->tipo_proprietario,
            'detalhes' => $request->detalhes
        ]);

        return redirect()->back()->with('success', 'Veículo atualizado com sucesso!');
    }

    public function destroy(Veiculo $veiculo)
    {
        $veiculo->delete();
        return redirect()->back()->with('success', 'Veículo excluído com sucesso!');
    }
}
