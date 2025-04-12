<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::with('vendedor')->get();
        return view('content.pages.customers.pages-customers', compact('clientes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'razao_social' => 'required|string|max:255',
            'cnpj' => 'required|string|max:18|unique:clientes,cnpj',
            'ie' => 'nullable|string|max:20',
            'endereco' => 'required|string|max:255',
            'codigo_ibge' => 'required|string|max:10',
            'telefone' => 'required|string|max:20',
            'contato' => 'required|string|max:255'
        ]);

        $cliente = Cliente::create([
            'razao_social' => $request->razao_social,
            'cnpj' => $request->cnpj,
            'ie' => $request->ie,
            'endereco' => $request->endereco,
            'codigo_ibge' => $request->codigo_ibge,
            'telefone' => $request->telefone,
            'contato' => $request->contato,
            'user_id' => auth()->id() // Registra o usuário que está cadastrando
        ]);

        return redirect()->back()->with('success', 'Cliente cadastrado com sucesso!');
    }

    public function detalhes(Cliente $cliente)
    {
        $cliente->load(['atendimentos.vendedor', 'arquivos.usuario', 'mensagens.usuario']);

        return response()->json([
            'razao_social' => $cliente->razao_social,
            'cnpj' => $cliente->cnpj,
            'telefone' => $cliente->telefone,
            'contato' => $cliente->contato,
            'interacoes' => $cliente->atendimentos->map(function($atendimento) {
                return [
                    'data' => $atendimento->data_atendimento->format('d/m/Y H:i'),
                    'descricao' => $atendimento->descricao,
                    'tipo' => $atendimento->tipo_atendimento,
                    'status' => $atendimento->status,
                    'vendedor' => $atendimento->vendedor->name
                ];
            }),
            'arquivos' => $cliente->arquivos->map(function($arquivo) {
                return [
                    'id' => $arquivo->id,
                    'nome' => $arquivo->nome_original,
                    'tamanho' => $arquivo->tamanho,
                    'tipo' => $arquivo->tipo,
                    'url' => Storage::url($arquivo->caminho),
                    'data' => $arquivo->created_at->format('d/m/Y H:i'),
                    'usuario' => $arquivo->usuario->name
                ];
            }),
            'mensagens' => $cliente->mensagens->map(function($mensagem) {
                return [
                    'id' => $mensagem->id,
                    'usuario' => $mensagem->usuario->name,
                    'mensagem' => $mensagem->conteudo,
                    'tipo' => $mensagem->user_id === auth()->id() ? 'sent' : 'received',
                    'data' => $mensagem->created_at->format('d/m/Y H:i'),
                    'lida' => $mensagem->lida
                ];
            })
        ]);
    }

    public function edit(Cliente $cliente)
    {
        try {
            return response()->json($cliente);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao carregar dados do cliente'], 500);
        }
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'razao_social' => 'required',
            'cnpj' => 'required',
            'endereco' => 'required',
            'codigo_ibge' => 'required',
            'telefone' => 'required',
            'contato' => 'required',
        ]);

        $cliente->update($validated);

        return redirect()->back()->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return redirect()->back()->with('success', 'Cliente excluído com sucesso!');
    }

    public function historico(Cliente $cliente)
    {
        $historicos = $cliente->historicos()->with('usuario')->orderBy('data', 'desc')->get()->map(function($historico) {
            return [
                'data' => $historico->data->format('d/m/Y H:i'),
                'vendedora' => $historico->usuario->name,
                'texto' => $historico->texto,
                'proxima_acao' => $historico->proxima_acao
            ];
        });

        return response()->json([
            'success' => true,
            'cliente' => [
                'razao_social' => $cliente->razao_social,
                'cnpj' => $cliente->cnpj,
                'telefone' => $cliente->telefone,
                'contato' => $cliente->contato,
                'endereco' => $cliente->endereco,
                'vendedora' => $cliente->vendedor->name ?? 'Não atribuído'
            ],
            'historicos' => $historicos
        ]);
    }

    public function storeHistorico(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'texto' => 'required|string',
            'proxima_acao' => 'nullable|string'
        ]);

        $historico = $cliente->historicos()->create([
            'user_id' => auth()->id(),
            'data' => now(),
            'texto' => $validated['texto'],
            'proxima_acao' => $validated['proxima_acao']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Histórico registrado com sucesso!',
            'historico' => [
                'data' => $historico->data->format('d/m/Y H:i'),
                'vendedora' => auth()->user()->name,
                'texto' => $historico->texto,
                'proxima_acao' => $historico->proxima_acao
            ]
        ]);
    }
}
