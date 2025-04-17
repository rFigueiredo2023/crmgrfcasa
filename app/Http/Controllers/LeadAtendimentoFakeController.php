<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use Illuminate\Http\Request;

class LeadAtendimentoFakeController extends Controller
{
    public function store(Request $request, $id)
    {
        // Apenas para teste - retornar sucesso
        return response()->json([
            'success' => true,
            'message' => 'Atendimento registrado com sucesso!',
            'debug' => [
                'lead_id' => $id,
                'request_data' => $request->all(),
                'method' => $request->method(),
                'url' => $request->url(),
                'path' => $request->path()
            ]
        ]);
    }
}
