<?php

namespace App\Http\Controllers;

use App\Models\Mensagem;
use App\Models\Cliente;
use Illuminate\Http\Request;

class MensagemController extends Controller
{
    public function store(Request $request, Cliente $cliente)
    {
        $request->validate([
            'conteudo' => 'required|string'
        ]);

        $mensagem = Mensagem::create([
            'cliente_id' => $cliente->id,
            'user_id' => auth()->id(),
            'conteudo' => $request->conteudo
        ]);

        return response()->json([
            'success' => true,
            'mensagem' => [
                'id' => $mensagem->id,
                'conteudo' => $mensagem->conteudo,
                'usuario' => $mensagem->usuario->name,
                'tipo' => 'sent',
                'created_at' => $mensagem->created_at->format('d/m/Y H:i')
            ]
        ]);
    }

    public function marcarComoLida(Mensagem $mensagem)
    {
        if (!$mensagem->lida) {
            $mensagem->update([
                'lida' => true,
                'lida_em' => now()
            ]);
        }

        return response()->json([
            'success' => true
        ]);
    }
}