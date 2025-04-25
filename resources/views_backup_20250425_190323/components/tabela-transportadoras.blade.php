{{-- Componente Tabela transportadoras --}}
<!-- Campo de busca e botão -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0">Tabela de Transportadoras</h5>

  <div class="d-flex align-items-center gap-2">
    <input type="text" class="form-control" id="busca-transportadora" placeholder="Buscar transportadora..." style="max-width: 220px" />
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddTransportadora">
      <i class="bx bx-plus"></i> Adicionar
    </button>
  </div>
</div>

<!-- Tabela -->
<div class="table-responsive">
  <table class="table table-hover">
    <thead>
      <tr>
        <th>Razão Social</th>
        <th>CNPJ</th>
        <th>Telefone</th>
        <th>Email</th>
        <th>Contato</th>
        <th>Ações</th>
      </tr>
    </thead>
    <tbody class="table-border-bottom-0">
      @forelse($transportadoras as $transportadora)
      <tr>
        <td>{{ $transportadora->razao_social }}</td>
        <td>{{ $transportadora->cnpj }}</td>
        <td>{{ $transportadora->telefone }}</td>
        <td>{{ $transportadora->email }}</td>
        <td>{{ $transportadora->contato }}</td>
        <td>
          <div class="dropdown">
            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
              <i class="bx bx-dots-vertical-rounded"></i>
            </button>
            <div class="dropdown-menu">
              <a class="dropdown-item" href="javascript:void(0);"
                 data-bs-toggle="modal"
                 data-bs-target="#modalEditTransportadora"
                 data-transportadora-id="{{ $transportadora->id }}">
                <i class="bx bx-edit-alt me-1"></i> Editar
              </a>
              <a class="dropdown-item" href="javascript:void(0);"
                 onclick="confirmarExclusao({{ $transportadora->id }})">
                <i class="bx bx-trash me-1"></i> Excluir
              </a>
            </div>
          </div>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="6" class="text-center">Nenhuma transportadora cadastrada</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

@push('modals')
  @include('components.form-transportadora')
@endpush

@push('scripts')
<script>
    // Script para busca
    (function() {
      const buscaInput = document.getElementById('busca-transportadora');
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
        if (confirm('Tem certeza que deseja excluir esta transportadora?')) {
            // Implementar a lógica de exclusão
        }
    }
</script>
@endpush
