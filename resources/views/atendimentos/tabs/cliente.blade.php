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
                        <th>VENDEDORA RESPONSÁVEL</th>
                        <th>ATENDIMENTO</th>
                        <th>HISTÓRICO</th>
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
                        <td>{{ $cliente->vendedor->name ?? 'Não atribuído' }}</td>
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
                            <button type="button" 
                                    class="btn btn-icon btn-secondary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalHistorico"
                                    data-cliente-id="{{ $cliente->id }}"
                                    data-bs-toggle="tooltip"
                                    data-bs-placement="top"
                                    title="Ver histórico completo">
                                <i class="bx bx-time"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Nenhum cliente encontrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('modals')
    @include('components.form-atendimento')

    <!-- Modal Histórico -->
    <div class="modal fade" id="modalHistorico" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Histórico do Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Dados do Cliente -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <strong>Razão Social:</strong>
                                        <span id="historico-razao-social"></span>
                                    </p>
                                    <p class="mb-1">
                                        <strong>CNPJ:</strong>
                                        <span id="historico-cnpj"></span>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Telefone:</strong>
                                        <span id="historico-telefone"></span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-1">
                                        <strong>Contato:</strong>
                                        <span id="historico-contato"></span>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Endereço:</strong>
                                        <span id="historico-endereco"></span>
                                    </p>
                                    <p class="mb-1">
                                        <strong>Vendedora Responsável:</strong>
                                        <span id="historico-vendedora"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline -->
                    <div class="card">
                        <h5 class="card-header">Linha do Tempo</h5>
                        <div class="card-body">
                            <ul class="timeline" id="timeline-historico">
                                @foreach($cliente->atendimentos->sortByDesc('data_atendimento') as $atendimento)
                                <li class="timeline-item timeline-item-transparent">
                                    <span class="timeline-point timeline-point-primary"></span>
                                    <div class="timeline-event">
                                        <div class="timeline-header">
                                            <h6 class="mb-0">{{ $atendimento->tipo_atendimento }}</h6>
                                            <small class="text-muted">{{ $atendimento->data_atendimento->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <div class="timeline-body">
                                            <p>{{ $atendimento->descricao }}</p>
                                            <div class="d-flex justify-content-between">
                                                <span class="badge bg-{{ $atendimento->status === 'Concluído' ? 'success' : ($atendimento->status === 'Pendente' ? 'warning' : 'info') }}">
                                                    {{ $atendimento->status }}
                                                </span>
                                                <small class="text-muted">Atendido por: {{ $atendimento->vendedor->name }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
<script>
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

    // Inicializa tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Modal de Histórico
    const modalHistorico = document.getElementById('modalHistorico');
    modalHistorico.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const clienteId = button.getAttribute('data-cliente-id');
        
        // Busca os dados do histórico
        fetch(`/clientes/${clienteId}/historico`)
            .then(response => response.json())
            .then(data => {
                // Preenche os dados do cliente
                document.getElementById('historico-razao-social').textContent = data.cliente.razao_social;
                document.getElementById('historico-cnpj').textContent = data.cliente.cnpj;
                document.getElementById('historico-telefone').textContent = data.cliente.telefone;
                document.getElementById('historico-contato').textContent = data.cliente.contato;
                document.getElementById('historico-endereco').textContent = data.cliente.endereco;
                document.getElementById('historico-vendedora').textContent = data.cliente.vendedora;

                // Limpa a timeline
                const timeline = document.getElementById('timeline-historico');
                timeline.innerHTML = '';

                // Preenche a timeline
                data.historicos.forEach(historico => {
                    const li = document.createElement('li');
                    li.className = 'timeline-item timeline-item-transparent';
                    
                    li.innerHTML = `
                        <span class="timeline-point timeline-point-primary"></span>
                        <div class="timeline-event">
                            <div class="timeline-header mb-1">
                                <h6 class="mb-0">${historico.data}</h6>
                                <small class="text-muted">${historico.vendedora}</small>
                            </div>
                            <p class="mb-2">${historico.texto}</p>
                            ${historico.proxima_acao ? `
                                <div class="d-flex flex-wrap">
                                    <div class="badge bg-label-primary">Próxima ação: ${historico.proxima_acao}</div>
                                </div>
                            ` : ''}
                        </div>
                    `;
                    
                    timeline.appendChild(li);
                });
            });
    });
</script>
@endpush