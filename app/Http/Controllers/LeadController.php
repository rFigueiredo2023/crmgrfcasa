<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'origem' => 'required|string|max:255',
            'status' => 'required|in:Frio,Morno,Quente',
            'observacoes' => 'nullable|string'
        ]);

        Lead::create($request->all());

        return redirect()->back()->with('success', 'Lead cadastrado com sucesso!');
    }

    public function update(Request $request, Lead $lead)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'origem' => 'required|string|max:255',
            'status' => 'required|in:Frio,Morno,Quente',
            'observacoes' => 'nullable|string'
        ]);

        $lead->update($request->all());

        return redirect()->back()->with('success', 'Lead atualizado com sucesso!');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return redirect()->back()->with('success', 'Lead excluído com sucesso!');
    }
}