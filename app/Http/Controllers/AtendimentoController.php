<?php

namespace App\Http\Controllers;

use App\Models\Atendimento;
use App\Models\Cliente;
use Illuminate\Http\Request;

class AtendimentoController extends Controller
{
    public function index()
    {
        $atendimentos = Atendimento::with(['vendedor', 'cliente'])->get();
        $clientes = Cliente::all();
        return view('content.pages.atendimentos.pages-atendimentos', compact('atendimentos', 'clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'data_atendimento' => 'required|date',
            'tipo_atendimento' => 'required|string',
            'descricao' => 'required|string',
            'status' => 'required|string',
        ]);

        $atendimento = new Atendimento();
        $atendimento->cliente_id = $request->cliente_id;
        $atendimento->vendedor_id = auth()->id();
        $atendimento->data_atendimento = $request->data_atendimento;
        $atendimento->tipo_atendimento = $request->tipo_atendimento;
        $atendimento->descricao = $request->descricao;
        $atendimento->status = $request->status;
        $atendimento->save();

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
} 