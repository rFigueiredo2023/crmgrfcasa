<?php

namespace App\Http\Controllers;

use App\Models\Segmento;
use Illuminate\Http\Request;

class SegmentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $segmentos = Segmento::orderBy('nome')->paginate(10);
        return view('segmentos.index', compact('segmentos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('segmentos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:segmentos,nome'
        ]);

        $segmento = Segmento::create([
            'nome' => $request->nome
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Segmento criado com sucesso!',
                'segmento' => $segmento
            ]);
        }

        return redirect()->route('segmentos.index')->with('success', 'Segmento criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Segmento $segmento)
    {
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($segmento);
        }

        return view('segmentos.edit', compact('segmento'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Segmento $segmento)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:segmentos,nome,' . $segmento->id
        ]);

        $segmento->update([
            'nome' => $request->nome
        ]);

        return redirect()->route('segmentos.index')->with('success', 'Segmento atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Segmento $segmento)
    {
        // Verificar se há clientes usando este segmento
        if ($segmento->clientes()->exists()) {
            return redirect()->route('segmentos.index')->with('error', 'Este segmento não pode ser excluído pois está sendo usado por clientes.');
        }

        $segmento->delete();
        return redirect()->route('segmentos.index')->with('success', 'Segmento excluído com sucesso!');
    }
}
