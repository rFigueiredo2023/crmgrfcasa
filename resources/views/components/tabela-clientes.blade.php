{{-- Componente Tabela clientes --}}
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
                      <button type="button" class="btn p-0" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="bx bx-dots-vertical-rounded"></i>
                      </button>
                      <ul class="dropdown-menu">
                          <li>
                              <a class="dropdown-item d-flex align-items-center" href="javascript:void(0);"
                                 data-bs-toggle="modal"
                                 data-bs-target="#modalHistoricoCliente"
                                 data-cliente-id="{{ $cliente->id }}">
                                  <i class="bx bx-history me-2"></i> Histórico
                              </a>
                          </li>
                          <li>
                              <a class="dropdown-item d-flex align-items-center" href="javascript:void(0);"
                                 data-bs-toggle="modal"
                                 data-bs-target="#modalEditCliente"
                                 data-id="{{ $cliente->id }}">
                                  <i class="bx bx-edit-alt me-2"></i> Editar
                              </a>
                          </li>
                          <li>
                              <a class="dropdown-item d-flex align-items-center"
                                 href="javascript:void(0);"
                                 onclick="confirmarExclusao({{ $cliente->id }})">
                                  <i class="bx bx-trash me-2"></i> Excluir
                              </a>
                          </li>
                      </ul>
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

@push('scripts')
<script>
    // Busca de clientes
    document.addEventListener('DOMContentLoaded', function() {
        const buscaClienteInput = document.getElementById('busca-cliente');
        if (buscaClienteInput) {
            buscaClienteInput.addEventListener('input', function(e) {
                const busca = e.target.value.toLowerCase();
                const linhas = document.querySelectorAll('tbody tr');

                linhas.forEach(function(linha) {
                    const texto = linha.textContent.toLowerCase();
                    linha.style.display = texto.includes(busca) ? '' : 'none';
                });
            });
        }

        // Função para confirmar e executar a exclusão
        function confirmarExclusao(id) {
            if (confirm('Tem certeza que deseja excluir este cliente?')) {
                // Criar um form dinâmico para enviar a requisição DELETE
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/customers/clientes/${id}`;

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';

                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = document.querySelector('meta[name="csrf-token"]').content;

                form.appendChild(methodInput);
                form.appendChild(tokenInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Inicialização do modal de edição
        const modalEditCliente = document.getElementById('modalEditCliente');
        if (modalEditCliente) {
            modalEditCliente.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const clienteId = button.getAttribute('data-id');
                const form = this.querySelector('#formEditCliente');

                if (!clienteId) {
                    console.error('ID do cliente não encontrado');
                    return;
                }

                // Atualiza a action do formulário
                form.action = `/customers/clientes/${clienteId}`;

                // Busca os dados do cliente via AJAX
                fetch(`/customers/clientes/${clienteId}/edit`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro na resposta da rede');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data) {
                            throw new Error('Dados do cliente não encontrados');
                        }

                        // Preenche os campos do formulário
                        form.querySelector('#edit_razao_social').value = data.razao_social || '';
                        form.querySelector('#edit_cnpj').value = data.cnpj || '';
                        form.querySelector('#edit_ie').value = data.ie || '';
                        form.querySelector('#edit_endereco').value = data.endereco || '';
                        form.querySelector('#edit_codigo_ibge').value = data.codigo_ibge || '';
                        form.querySelector('#edit_telefone').value = data.telefone || '';
                        form.querySelector('#edit_contato').value = data.contato || '';
                        form.querySelector('#edit_segmento').value = data.segmento || '';
                    })
                    .catch(error => {
                        console.error('Erro ao carregar dados do cliente:', error);
                        alert('Erro ao carregar dados do cliente');
                    });
            });
        }
    });
</script>
@endpush

<!-- Modal de Edição -->
<div class="modal fade" id="modalEditCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-dialog-centered">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Editar Cliente</h3>
                    <p class="text-muted">Altere os dados do cliente</p>
                </div>

                <form id="formEditCliente" class="row g-3" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="col-md-6">
                        <label class="form-label" for="edit_razao_social">Razão Social</label>
                        <input type="text" class="form-control" id="edit_razao_social" name="razao_social" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="edit_cnpj">CNPJ</label>
                        <input type="text" class="form-control" id="edit_cnpj" name="cnpj" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="edit_ie">Inscrição Estadual</label>
                        <input type="text" class="form-control" id="edit_ie" name="ie">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="edit_endereco">Endereço</label>
                        <input type="text" class="form-control" id="edit_endereco" name="endereco" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="edit_codigo_ibge">Código IBGE</label>
                        <input type="text" class="form-control" id="edit_codigo_ibge" name="codigo_ibge" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="edit_telefone">Telefone</label>
                        <input type="text" class="form-control" id="edit_telefone" name="telefone" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label" for="edit_contato">Contato</label>
                        <input type="text" class="form-control" id="edit_contato" name="contato" required>
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">Atualizar</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
