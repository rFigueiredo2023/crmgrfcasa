<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Clientes</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="busca-cliente" placeholder="Buscar cliente...">
                    <button class="btn btn-outline-primary" type="button">
                        <i class="bx bx-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>RAZÃO SOCIAL</th>
                        <th>CNPJ</th>
                        <th>TELEFONE</th>
                        <th>CONTATO</th>
                        <th>ÚLTIMO ATENDIMENTO</th>
                        <th>ATENDIMENTO</th>
                        <th>AÇÕES</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->razao_social }}</td>
                        <td>{{ $cliente->cnpj }}</td>
                        <td>{{ $cliente->telefone }}</td>
                        <td>{{ $cliente->contato }}</td>
                        <td>
                            @if($cliente->ultimoAtendimento)
                                {{ $cliente->ultimoAtendimento->data_atendimento->format('d/m/Y H:i') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-info btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalAtendimento"
                                    data-cliente-id="{{ $cliente->id }}"
                                    data-cliente-nome="{{ $cliente->razao_social }}">
                                <i class="bx bx-headphone me-1"></i>Atendimento
                            </button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-primary btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalAtendimento"
                                    data-cliente-id="{{ $cliente->id }}"
                                    data-cliente-nome="{{ $cliente->razao_social }}">
                                <i class="bx bx-plus me-1"></i>Novo Atendimento
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Nenhum cliente encontrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Novo Atendimento -->
<div class="modal fade" id="modalAtendimento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Atendimento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('atendimentos.store') }}" method="POST">
                @csrf
                <input type="hidden" name="cliente_id" id="cliente_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Cliente</label>
                        <input type="text" class="form-control" id="cliente_nome" readonly>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Data do Atendimento</label>
                            <input type="datetime-local" class="form-control" name="data_atendimento" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipo de Atendimento</label>
                            <select class="form-select" name="tipo_atendimento" required>
                                <option value="">Selecione o tipo</option>
                                <option value="Visita">Visita</option>
                                <option value="Telefone">Telefone</option>
                                <option value="Email">Email</option>
                                <option value="WhatsApp">WhatsApp</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="">Selecione o status</option>
                            <option value="Pendente">Pendente</option>
                            <option value="Em Andamento">Em Andamento</option>
                            <option value="Concluído">Concluído</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <textarea class="form-control" name="descricao" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Preenche os dados do cliente no modal
        const modal = document.getElementById('modalAtendimento');
        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const clienteId = button.getAttribute('data-cliente-id');
            const clienteNome = button.getAttribute('data-cliente-nome');

            document.getElementById('cliente_id').value = clienteId;
            document.getElementById('cliente_nome').value = clienteNome;
        });

        // Busca de clientes
        const buscaInput = document.getElementById('busca-cliente');
        buscaInput.addEventListener('input', function(e) {
            const busca = e.target.value.toLowerCase();
            const linhas = document.querySelectorAll('tbody tr');

            linhas.forEach(function(linha) {
                const texto = linha.textContent.toLowerCase();
                linha.style.display = texto.includes(busca) ? '' : 'none';
            });
        });
    });
</script>
@endpush