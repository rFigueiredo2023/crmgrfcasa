<!-- Campo de busca e botão -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0">Tabela de Veículos</h5>

  <div class="d-flex align-items-center gap-2">
    <input type="text" class="form-control" placeholder="Buscar veículo..." style="max-width: 220px" />
    <button class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddVeiculo">
      <i class="bx bx-plus"></i> Adicionar
    </button>
  </div>
</div>

<!-- Tabela -->
<div class="table-responsive">
  <table class="table">
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