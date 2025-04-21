<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpenAIService;

class AssistenteController extends Controller
{
    /**
     * Exibe a página inicial do assistente
     */
    public function index()
    {
        return view('dev-assistente');
    }

    /**
     * Envia pergunta para a API da OpenAI e retorna a resposta
     */
    public function perguntar(Request $request)
    {
        $request->validate([
            'prompt' => 'required|min:10',
        ], [
            'prompt.required' => 'Por favor, descreva seu problema ou dúvida.',
            'prompt.min' => 'A pergunta deve ter pelo menos 10 caracteres.',
        ]);

        $prompt = $request->input('prompt');
        $resposta = (new OpenAIService())->ask($prompt);

        return view('dev-assistente', [
            'prompt' => $prompt,
            'resposta' => $resposta,
        ]);
    }
}
