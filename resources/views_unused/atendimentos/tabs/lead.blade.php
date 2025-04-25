{{-- View de Lead relacionada a atendimentos/tabs --}}
<button type="button"
        class="btn btn-icon btn-secondary"
        data-historico
        data-tipo="lead"
        data-id="{{ $lead->id }}"
        data-nome="{{ $lead->nome }}"
        title="Ver histórico completo">
    <i class="bx bx-time"></i>
</button>
