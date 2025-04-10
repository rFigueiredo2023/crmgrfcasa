<?php

namespace App\Http\Controllers;

use App\Models\Arquivo;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArquivoController extends Controller
{
    public function store(Request $request, Cliente $cliente)
    {
        $request->validate([
            'arquivo' => 'required|file|max:10240' // máximo 10MB
        ]);

        $arquivo = $request->file('arquivo');
        $nomeOriginal = $arquivo->getClientOriginalName();
        $nome = time() . '_' . $nomeOriginal;
        $tipo = $arquivo->getMimeType();
        $tamanho = $this->formatBytes($arquivo->getSize());

        $caminho = $arquivo->storeAs(
            'arquivos/clientes/' . $cliente->id,
            $nome,
            'public'
        );

        $arquivo = Arquivo::create([
            'cliente_id' => $cliente->id,
            'user_id' => auth()->id(),
            'nome' => $nome,
            'nome_original' => $nomeOriginal,
            'tipo' => $tipo,
            'tamanho' => $tamanho,
            'caminho' => $caminho
        ]);

        return response()->json([
            'success' => true,
            'arquivo' => $arquivo
        ]);
    }

    public function destroy(Arquivo $arquivo)
    {
        if ($arquivo->user_id !== auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado'
            ], 403);
        }

        Storage::disk('public')->delete($arquivo->caminho);
        $arquivo->delete();

        return response()->json([
            'success' => true
        ]);
    }

    private function formatBytes($bytes)
    {
        if ($bytes > 1024 * 1024) {
            return round($bytes / (1024 * 1024), 2) . ' MB';
        } elseif ($bytes > 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }
}