<!-- Campo de busca e botão -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0">Tabela de Clientes</h5>

  <div class="d-flex align-items-center gap-2">
      <input type="text" class="form-control" placeholder="Buscar cliente..." style="max-width: 220px" />
      <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddCliente">
          <i class="bx bx-plus"></i> Adicionar
      </button>
  </div>
</div>

<!-- Tabela -->
<div class="table-responsive">
  <table class="datatables-users table">
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

@push('modals')
  @include('components.form-cliente')
@endpush