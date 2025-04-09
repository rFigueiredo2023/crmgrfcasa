<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Tabela de Clientes</h5>

        <div class="d-flex align-items-center gap-2">
            <!-- Campo de busca -->
            <input type="text" class="form-control" placeholder="Buscar cliente..." style="max-width: 220px" />

            <!-- Botão de adicionar -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddCliente">
                <i class="bx bx-plus"></i> Adicionar
            </button>
        </div>
    </div>

    <div class="card-datatable table-responsive">
        <table class="datatables-users table border-top">
            <thead>
                <tr>
                    <th></th>
                    <th></th>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>Contato</th>
                    <th>Status</th>
                    <th>Ações</th>
                    <th>Vendedor</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<!-- Offcanvas fora do card -->
<!-- <x-offcanvas-add-cliente /> -->

@push('modals')
    @include('components.form-cliente')
@endpush
