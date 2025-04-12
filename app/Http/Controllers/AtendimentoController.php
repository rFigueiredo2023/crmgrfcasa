<?php

namespace App\Http\Controllers;

use App\Models\Atendimento;
use App\Models\Cliente;
use App\Models\Lead;
use Illuminate\Http\Request;

class AtendimentoController extends Controller
{
    public function index()
    {
        $atendimentos = Atendimento::with(['vendedor', 'cliente'])->get();
        $clientes = Cliente::all();
        $leads = Lead::all();

        return view('content.pages.atendimentos.pages-atendimentos',
            compact('atendimentos', 'clientes', 'leads')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo' => 'required|string',
            'descricao' => 'required|string',
            'proxima_acao' => 'nullable|string',
            'data' => 'required|date',
            'status' => 'required|string'
        ]);

        $atendimento = Atendimento::create([
            'cliente_id' => $validated['cliente_id'],
            'user_id' => auth()->id(),
            'tipo' => $validated['tipo'],
            'descricao' => $validated['descricao'],
            'proxima_acao' => $validated['proxima_acao'],
            'data' => $validated['data'],
            'status' => $validated['status']
        ]);

        return redirect()->route('atendimentos.index')->with('success', 'Atendimento registrado com sucesso!');
    }

    public function search(Request $request)
    {
        $query = Atendimento::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('cliente', function($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%");
            })
            ->orWhere('tipo_atendimento', 'like', "%{$search}%")
            ->orWhere('status', 'like', "%{$search}%");
        }

        $atendimentos = $query->with(['vendedor', 'cliente'])->get();
        return view('content.pages.atendimentos.pages-atendimentos', compact('atendimentos'));
    }

    public function update(Request $request, Atendimento $atendimento)
    {
        $validated = $request->validate([
            'tipo' => 'required|string',
            'descricao' => 'required|string',
            'proxima_acao' => 'nullable|string',
            'data' => 'required|date',
            'status' => 'required|string'
        ]);

        $atendimento->update($validated);

        return redirect()->route('atendimentos.index')->with('success', 'Atendimento atualizado com sucesso!');
    }
}