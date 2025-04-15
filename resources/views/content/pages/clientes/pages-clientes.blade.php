@include('components.modal-historico-cliente')

<button type="button"
        class="btn btn-sm btn-icon btn-secondary"
        data-bs-toggle="modal"
        data-bs-target="#modalHistorico"
        data-cliente-id="{{ $cliente->id }}"
        data-cliente-nome="{{ $cliente->razao_social }}">
    <i class="bx bx-history"></i>
</button>