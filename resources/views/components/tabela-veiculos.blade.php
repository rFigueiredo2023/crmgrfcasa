<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0">Tabela de Veículos</h5>

        <div class="d-flex align-items-center gap-2">
            <!-- Campo de busca -->
            <input type="text" class="form-control" placeholder="Buscar veículo..." style="max-width: 220px" />

            <!-- Botão de adicionar -->
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddVeiculo">
                <i class="bx bx-plus"></i> Adicionar
            </button>
        </div>
    </div>

    <div class="card-datatable table-responsive">
        <table class="table border-top">
            <thead>
                <tr>
                    <th></th>
                    <th>Placa</th>
                    <th>Modelo</th>
                    <th>Transportadora</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

@push('modals')
    @include('components.form-veiculo')
@endpush
