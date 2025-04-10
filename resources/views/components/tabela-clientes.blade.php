<!-- Campo de busca e botão -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="mb-0">Tabela de Clientes</h5>

  <div class="d-flex align-items-center gap-2">
      <input type="text" class="form-control" id="busca-cliente" placeholder="Buscar cliente..." style="max-width: 220px" />
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddCliente">
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
              <th>Contato</th>
              <th>Vendedor</th>
              <th>Ações</th>
          </tr>
      </thead>
      <tbody class="table-border-bottom-0">
          @forelse($clientes as $cliente)
          <tr>
              <td>{{ $cliente->razao_social }}</td>
              <td>{{ $cliente->cnpj }}</td>
              <td>{{ $cliente->telefone }}</td>
              <td>{{ $cliente->contato }}</td>
              <td>{{ $cliente->vendedor ? $cliente->vendedor->name : '-' }}</td>
              <td>
                  <div class="dropdown">
                      <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                          <i class="bx bx-dots-vertical-rounded"></i>
                      </button>
                      <div class="dropdown-menu">
                          <a class="dropdown-item" href="javascript:void(0);"
                             data-bs-toggle="modal"
                             data-bs-target="#modalEditCliente"
                             data-cliente-id="{{ $cliente->id }}">
                              <i class="bx bx-edit-alt me-1"></i> Editar
                          </a>
                          <a class="dropdown-item" href="javascript:void(0);"
                             onclick="confirmarExclusao({{ $cliente->id }})">
                              <i class="bx bx-trash me-1"></i> Excluir
                          </a>
                      </div>
                  </div>
              </td>
          </tr>
          @empty
          <tr>
              <td colspan="6" class="text-center">Nenhum cliente cadastrado</td>
          </tr>
          @endforelse
      </tbody>
  </table>
</div>

@push('modals')
  @include('components.form-cliente')
@endpush

@push('scripts')
<script>
    // Busca de clientes
    const buscaInput = document.getElementById('busca-cliente');
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

    // Função para confirmar exclusão
    function confirmarExclusao(id) {
        if (confirm('Tem certeza que deseja excluir este cliente?')) {
            // Implementar a lógica de exclusão
        }
    }
</script>
@endpush