{{-- Página de edição de content/pages/atendimentos --}}
<div class="mb-3">
    <label for="descricao" class="form-label">Descrição</label>
    <textarea class="form-control" id="descricao" name="descricao" rows="3" required>{{ $atendimento->descricao }}</textarea>
</div>

<div class="mb-3">
    <label for="proxima_acao" class="form-label">Próxima Ação</label>
    <textarea class="form-control" id="proxima_acao" name="proxima_acao" rows="2">{{ $atendimento->proxima_acao }}</textarea>
</div>

<div class="mb-3">
    <label for="data" class="form-label">Data</label>
    <input type="date" class="form-control" id="data" name="data" value="{{ $atendimento->data->format('Y-m-d') }}" required>
</div> 