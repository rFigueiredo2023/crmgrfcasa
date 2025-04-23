<!-- Campo de busca e botão -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0">Tabela de Veículos</h5>

  <div class="d-flex align-items-center gap-2">
    <input type="text" class="form-control" id="busca-veiculo" placeholder="Buscar veículo..." style="max-width: 220px" />
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddVeiculo">
      <i class="bx bx-plus"></i> Adicionar
    </button>
  </div>
</div>

<!-- Tabela -->
<div class="table-responsive">
  <table class="table table-hover">
    <thead>
      <tr>
        <th>Placa</th>
        <th>Motorista</th>
        <th>Marca/Modelo</th>
        <th>Tipo</th>
        <th>Capacidade</th>
        <th>Status</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody class="table-border-bottom-0">
      @forelse($veiculos as $veiculo)
      <tr>
        <td>{{ $veiculo->placa }}</td>
        <td>{{ $veiculo->motorista }}</td>
        <td>{{ $veiculo->marca }} {{ $veiculo->modelo }}</td>
        <td>
          <span class="badge bg-label-primary">{{ $veiculo->tipo_rodagem }}</span>
          <span class="badge bg-label-info">{{ $veiculo->tipo_carroceria }}</span>
        </td>
        <td>
          <small class="d-block">{{ number_format($veiculo->capacidade_kg, 0, ',', '.') }} kg</small>
          <small class="d-block">{{ number_format($veiculo->capacidade_m3, 1, ',', '.') }} m³</small>
        </td>
        <td>
          <span class="badge bg-success">Ativo</span>
        </td>
        <td>
          <div class="dropdown">
            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
              <i class="bx bx-dots-vertical-rounded"></i>
            </button>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="javascript:void(0);"
                 data-bs-toggle="modal"
                 data-bs-target="#modalEditVeiculo"
                 data-veiculo-id="{{ $veiculo->id }}">
                <i class="bx bx-edit-alt me-1"></i> Editar
              </a>
              <a class="dropdown-item" href="javascript:void(0);"
                 onclick="confirmarExclusao({{ $veiculo->id }})">
                <i class="bx bx-trash me-1"></i> Excluir
              </a>
              <a class="dropdown-item" href="javascript:void(0);"
                 onclick="verDetalhes({{ $veiculo->id }})">
                <i class="bx bx-info-circle me-1"></i> Detalhes
              </a>
            </div>
          </div>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="7" class="text-center">Nenhum veículo cadastrado</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

@push('modals')
  @include('components.form-veiculo')
@endpush

@push('scripts')
<script>
    // Script para busca
    (function() {
      const buscaInput = document.getElementById('busca-veiculo');
      if (buscaInput) {
        buscaInput.addEventListener('input', function(e) {
          const busca = e.target.value.toLowerCase();
          const linhas = document.querySelectorAll('tbody tr');

          linhas.forEach(function(linha) {
            const texto = linha.textContent.toLowerCase();
            linha.style.display = texto.includes(busca) ? '' : 'none';
          });
        });
      }
    })();

    // Função para confirmar exclusão
    function confirmarExclusao(id) {
        if (confirm('Tem certeza que deseja excluir este veículo?')) {
            // Implementar a lógica de exclusão
        }
    }

    // Função para ver detalhes do veículo
    function verDetalhes(id) {
        // Implementar a lógica para mostrar detalhes do veículo
        // Pode ser um novo modal ou uma página separada
    }
</script>
@endpush
