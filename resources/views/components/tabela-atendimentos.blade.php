<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Atendimentos</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAtendimento">
            <i class="bx bx-plus me-1"></i>Novo Atendimento
        </button>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <form action="{{ route('atendimentos.search') }}" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Buscar atendimentos..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bx bx-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach($atendimentos as $atendimento)
                    <tr>
                        <td>{{ $atendimento->cliente->nome }}</td>
                        <td>{{ $atendimento->vendedor->name }}</td>
                        <td>{{ $atendimento->data_atendimento->format('d/m/Y H:i') }}</td>
                        <td>{{ $atendimento->tipo_atendimento }}</td>
                        <td>
                            <span class="badge bg-{{ $atendimento->status === 'Concluído' ? 'success' : ($atendimento->status === 'Pendente' ? 'warning' : 'info') }}">
                                {{ $atendimento->status }}
                            </span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalAtendimento{{ $atendimento->id }}">
                                        <i class="bx bx-edit-alt me-1"></i> Editar
                                    </a>
                                    <a class="dropdown-item" href="javascript:void(0);" onclick="confirmDelete({{ $atendimento->id }})">
                                        <i class="bx bx-trash me-1"></i> Excluir
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
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
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Cliente</label>
                            <select class="form-select" name="cliente_id" required>
                                <option value="">Selecione um cliente</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}">{{ $cliente->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Data do Atendimento</label>
                            <input type="datetime-local" class="form-control" name="data_atendimento" required>
                        </div>
                    </div>
                    <div class="row">
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
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="">Selecione o status</option>
                                <option value="Pendente">Pendente</option>
                                <option value="Em Andamento">Em Andamento</option>
                                <option value="Concluído">Concluído</option>
                            </select>
                        </div>
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