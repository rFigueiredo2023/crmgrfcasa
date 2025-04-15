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
                                {{ $cliente->ultimoAtendimento->created_at->format('d/m/Y H:i') }}
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
                                    data-cliente-nome="{{ $cliente->razao_social }}"
                                    title="Ver histórico completo">
                                <i class="bx bx-history"></i>
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

<!-- Modal de Histórico -->
<div class="modal fade" id="modalHistorico" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Histórico de Atendimentos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="cliente-info mb-4">
                    <h6 class="mb-1">Cliente: <span id="cliente_nome_historico"></span></h6>
                </div>
                <div id="timeline_atendimentos">
                    <!-- Conteúdo será carregado via JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('modals')
    @include('components.form-atendimento')
    @include('components.modal-historico-cliente')
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

    document.addEventListener('DOMContentLoaded', function() {
        // Seleciona todos os botões de histórico
        const botoesHistorico = document.querySelectorAll('[data-bs-target="#modalHistorico"]');

        // Inicializa o modal uma única vez
        const modalElement = document.getElementById('modalHistorico');
        const modalHistorico = new bootstrap.Modal(modalElement, {
            backdrop: true,
            keyboard: true,
            focus: true
        });

        // Adiciona o evento de clique em cada botão
        botoesHistorico.forEach(botao => {
            botao.addEventListener('click', function(e) {
                e.preventDefault();

                const clienteId = this.getAttribute('data-cliente-id');
                const clienteNome = this.getAttribute('data-cliente-nome');

                // Atualiza o nome do cliente no modal
                document.getElementById('cliente_nome_historico').textContent = clienteNome;

                // Mostra loading
                document.getElementById('timeline_atendimentos').innerHTML = `
                    <div class="text-center py-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-2">Carregando histórico...</p>
                    </div>
                `;

                // Abre o modal
                modalHistorico.show();

                // Busca os dados
                fetch(`/customers/api/clientes/${clienteId}/atendimentos`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro ao carregar dados');
                        }
                        return response.json();
                    })
                    .then(data => {
                        let html = '';

                        if (data.length === 0) {
                            html = '<p class="text-center text-muted">Nenhum atendimento encontrado.</p>';
                        } else {
                            data.forEach(atendimento => {
                                const dataFormatada = new Date(atendimento.created_at).toLocaleDateString('pt-BR', {
                                    day: '2-digit',
                                    month: '2-digit',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                });

                                html += `
                                    <div class="border-start border-3 border-primary ps-3 mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-primary">${atendimento.tipo_contato}</span>
                                            <small class="text-muted">${dataFormatada}</small>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Status:</strong>
                                            <span class="badge bg-${getStatusColor(atendimento.status)}">
                                                ${atendimento.status}
                                            </span>
                                        </div>
                                        <p class="mb-2"><strong>Descrição:</strong> ${atendimento.descricao}</p>
                                        ${atendimento.retorno ? `<p class="mb-2"><strong>Retorno:</strong> ${atendimento.retorno}</p>` : ''}
                                        ${atendimento.proxima_acao ? `<p class="mb-2"><strong>Próxima Ação:</strong> ${atendimento.proxima_acao}</p>` : ''}
                                        ${atendimento.anexo ? `
                                            <div class="mt-2">
                                                <a href="/storage/${atendimento.anexo}" class="btn btn-sm btn-outline-primary" target="_blank">
                                                    <i class="bx bx-download"></i> Anexo
                                                </a>
                                            </div>
                                        ` : ''}
                                    </div>
                                `;
                            });
                        }

                        document.getElementById('timeline_atendimentos').innerHTML = html;
                    })
                    .catch(error => {
                        document.getElementById('timeline_atendimentos').innerHTML = `
                            <div class="alert alert-danger" role="alert">
                                Erro ao carregar o histórico. Tente novamente.
                            </div>
                        `;
                    });
            });
        });

        function getStatusColor(status) {
            const colors = {
                'ABERTO': 'primary',
                'EM_ANDAMENTO': 'warning',
                'CONCLUIDO': 'success'
            };
            return colors[status] || 'secondary';
        }
    });
</script>
@endpush