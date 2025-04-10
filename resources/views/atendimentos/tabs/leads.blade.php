<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Leads</h5>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovoLead">
            <i class="bx bx-plus"></i> Novo Lead
        </button>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="busca-lead" placeholder="Buscar lead...">
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
                        <th>Nome/Empresa</th>
                        <th>Telefone</th>
                        <th>Email</th>
                        <th>Origem</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($leads as $lead)
                    <tr>
                        <td>{{ $lead->nome }}</td>
                        <td>{{ $lead->telefone }}</td>
                        <td>{{ $lead->email }}</td>
                        <td>{{ $lead->origem }}</td>
                        <td>
                            <span class="badge bg-{{ $lead->status === 'Quente' ? 'danger' : ($lead->status === 'Morno' ? 'warning' : 'info') }}">
                                {{ $lead->status }}
                            </span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalAtendimento"
                                       data-lead-id="{{ $lead->id }}" data-lead-nome="{{ $lead->nome }}">
                                        <i class="bx bx-plus me-1"></i> Novo Atendimento
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalEditarLead"
                                       data-lead-id="{{ $lead->id }}">
                                        <i class="bx bx-edit-alt me-1"></i> Editar
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="confirmarExclusao({{ $lead->id }})">
                                        <i class="bx bx-trash me-1"></i> Excluir
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Nenhum lead encontrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Novo Lead -->
<div class="modal fade" id="modalNovoLead" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('leads.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nome/Empresa</label>
                            <input type="text" class="form-control" name="nome" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="text" class="form-control phone-mask" name="telefone" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Origem</label>
                            <select class="form-select" name="origem" required>
                                <option value="">Selecione a origem</option>
                                <option value="Site">Site</option>
                                <option value="Indicação">Indicação</option>
                                <option value="Redes Sociais">Redes Sociais</option>
                                <option value="Outros">Outros</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status" required>
                            <option value="">Selecione o status</option>
                            <option value="Frio">Frio</option>
                            <option value="Morno">Morno</option>
                            <option value="Quente">Quente</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Observações</label>
                        <textarea class="form-control" name="observacoes" rows="3"></textarea>
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
    // Máscara para telefone
    document.querySelectorAll('.phone-mask').forEach(function(element) {
        element.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{4,5})(\d)/, '$1-$2');
                e.target.value = value;
            }
        });
    });

    // Função para confirmar exclusão
    function confirmarExclusao(id) {
        if (confirm('Tem certeza que deseja excluir este lead?')) {
            // Implementar lógica de exclusão
        }
    }

    // Busca de leads
    document.getElementById('busca-lead').addEventListener('input', function(e) {
        const busca = e.target.value.toLowerCase();
        const linhas = document.querySelectorAll('tbody tr');

        linhas.forEach(function(linha) {
            const texto = linha.textContent.toLowerCase();
            linha.style.display = texto.includes(busca) ? '' : 'none';
        });
    });
</script>
@endpush

