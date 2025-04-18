<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Atendimento;
use App\Models\Lead;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Enums\StatusAtendimento;

class AtendimentoController extends Controller
{
    /**
     * Obter um atendimento específico
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        try {
            $atendimento = Atendimento::findOrFail($id);
            return response()->json($atendimento);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Erro ao buscar atendimento: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Salvar um novo atendimento para cliente
     *
     * @param Request $request
     * @param Cliente $cliente
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeClienteAtendimento(Request $request, Cliente $cliente)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'tipo_contato' => 'required|string',
                'descricao' => 'required|string',
                'status' => ['required', Rule::in(StatusAtendimento::values())],
                'retorno' => 'nullable|string',
                'data_retorno' => 'nullable|date',
                'proxima_acao' => 'nullable|string',
                'data_proxima_acao' => 'nullable|date',
                'ativar_lembrete' => 'nullable|boolean',
                'anexo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:5120'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Criar o atendimento
            $atendimento = new Atendimento();
            $atendimento->cliente_id = $cliente->id;
            $atendimento->user_id = auth()->id();
            $atendimento->tipo_contato = $request->tipo_contato;
            $atendimento->descricao = $request->descricao;
            $atendimento->status = $request->status;
            $atendimento->retorno = $request->retorno;
            $atendimento->data_retorno = $request->data_retorno;
            $atendimento->proxima_acao = $request->proxima_acao;
            $atendimento->data_proxima_acao = $request->data_proxima_acao;
            $atendimento->ativar_lembrete = $request->boolean('ativar_lembrete');

            if ($request->hasFile('anexo')) {
                $path = $request->file('anexo')->store('atendimentos/anexos', 'public');
                $atendimento->anexo = $path;
            }

            $atendimento->save();

            // Criar o histórico usando o relacionamento polimórfico
            $cliente->historicos()->create([
                'user_id' => auth()->id(),
                'tipo' => $request->tipo_contato,
                'texto' => $request->descricao,
                'proxima_acao' => $request->proxima_acao,
                'data_proxima_acao' => $request->data_proxima_acao,
                'retorno' => $request->retorno,
                'data_retorno' => $request->data_retorno,
                'ativar_lembrete' => $request->boolean('ativar_lembrete'),
                'data' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Atendimento registrado com sucesso!',
                'atendimento' => $atendimento
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar atendimento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Salvar um novo atendimento para lead
     *
     * @param Request $request
     * @param Lead $lead
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeLeadAtendimento(Request $request, Lead $lead)
    {
        try {
            DB::beginTransaction();

            $validator = Validator::make($request->all(), [
                'tipo_contato' => 'required|string',
                'texto' => 'required|string',
                'proxima_acao' => 'nullable|string',
                'data_proxima_acao' => 'nullable|date',
                'retorno' => 'nullable|string',
                'data_retorno' => 'nullable|date',
                'ativar_lembrete' => 'nullable|boolean',
                'anexo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:5120'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Criar o histórico usando o relacionamento polimórfico
            $historico = $lead->historicos()->create([
                'user_id' => auth()->id(),
                'tipo' => $request->tipo_contato,
                'texto' => $request->texto,
                'proxima_acao' => $request->proxima_acao,
                'data_proxima_acao' => $request->data_proxima_acao,
                'retorno' => $request->retorno,
                'data_retorno' => $request->data_retorno,
                'ativar_lembrete' => $request->boolean('ativar_lembrete'),
                'data' => now()
            ]);

            // Se tiver anexo, salvar
            if ($request->hasFile('anexo')) {
                $path = $request->file('anexo')->store('atendimentos/anexos', 'public');
                $historico->anexo = $path;
                $historico->save();
            }

            // Atualizar status do lead se fornecido
            if ($request->has('status') && $request->status) {
                $lead->status = $request->status;
                $lead->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Atendimento registrado com sucesso!',
                'historico' => $historico
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao registrar atendimento: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Atualizar um atendimento existente
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $atendimento = Atendimento::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'tipo_contato' => 'required|string',
                'descricao' => 'required|string',
                'status' => ['required', Rule::in(StatusAtendimento::values())],
                'retorno' => 'nullable|string',
                'data_retorno' => 'nullable|date',
                'proxima_acao' => 'nullable|string',
                'data_proxima_acao' => 'nullable|date',
                'ativar_lembrete' => 'nullable|boolean',
                'anexo' => 'nullable|file|mimes:pdf,jpg,jpeg,png,gif|max:5120'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }

            // Atualizar o atendimento
            $atendimento->tipo_contato = $request->tipo_contato;
            $atendimento->descricao = $request->descricao;
            $atendimento->status = $request->status;
            $atendimento->retorno = $request->retorno;
            $atendimento->data_retorno = $request->data_retorno;
            $atendimento->proxima_acao = $request->proxima_acao;
            $atendimento->data_proxima_acao = $request->data_proxima_acao;
            $atendimento->ativar_lembrete = $request->boolean('ativar_lembrete');

            if ($request->hasFile('anexo')) {
                // Remover anexo antigo se existir
                if ($atendimento->anexo) {
                    Storage::disk('public')->delete($atendimento->anexo);
                }

                $path = $request->file('anexo')->store('atendimentos/anexos', 'public');
                $atendimento->anexo = $path;
            }

            $atendimento->save();

            // Adicionar ao histórico da entidade relacionada
            if ($atendimento->cliente_id) {
                $cliente = Cliente::find($atendimento->cliente_id);
                $cliente->historicos()->create([
                    'user_id' => auth()->id(),
                    'tipo' => $request->tipo_contato,
                    'texto' => "Atendimento atualizado: " . $request->descricao,
                    'proxima_acao' => $request->proxima_acao,
                    'data' => now()
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Atendimento atualizado com sucesso!',
                'atendimento' => $atendimento
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar atendimento: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getAtendimentosLead(Lead $lead)
    {
        try {
            $atendimentos = Atendimento::where('lead_id', $lead->id)
                ->with(['user', 'anexos'])
                ->orderBy('data_atendimento', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $atendimentos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar atendimentos do lead',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function historico(Cliente $cliente)
    {
        try {
            $atendimentos = $cliente->atendimentos()
                ->with(['user']) // Carrega o relacionamento com o usuário
                ->orderBy('data_atendimento', 'desc')
                ->get()
                ->map(function ($atendimento) {
                    return [
                        'id' => $atendimento->id,
                        'tipo_contato' => $atendimento->tipo_contato,
                        'descricao' => $atendimento->descricao,
                        'data_atendimento' => $atendimento->data_atendimento,
                        'status' => $atendimento->status,
                        'retorno' => $atendimento->retorno,
                        'data_retorno' => $atendimento->data_retorno,
                        'proxima_acao' => $atendimento->proxima_acao,
                        'vendedor' => $atendimento->user->name ?? 'Não atribuído'
                    ];
                });

            return response()->json($atendimentos);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erro ao carregar os atendimentos'], 500);
        }
    }
}
