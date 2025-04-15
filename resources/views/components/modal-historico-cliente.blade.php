<!-- Modal de Histórico -->
<div class="modal fade" id="modalHistorico" tabindex="-1" aria-labelledby="modalHistoricoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHistoricoLabel">Histórico de Atendimentos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="cliente-info mb-4">
                    <h6 class="mb-1">Cliente: <span id="cliente_nome_historico"></span></h6>
                </div>
                <div class="timeline-wrapper">
                    <div class="timeline-list" id="timeline_atendimentos">
                        <!-- Os atendimentos serão inseridos aqui via JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline-wrapper {
    position: relative;
    padding: 1rem;
}

.timeline-list {
    position: relative;
    padding: 0;
    margin: 0;
}

.timeline-list:before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    height: 100%;
    width: 2px;
    background: #e0e0e0;
}

.timeline-item {
    position: relative;
    padding: 1rem 1rem 1rem 3rem;
    margin-bottom: 1.5rem;
    border-radius: 0.5rem;
    background: #f8f9fa;
}

.timeline-item:before {
    content: '';
    position: absolute;
    left: 0.5rem;
    top: 1.5rem;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    background: #696cff;
    border: 2px solid #fff;
}

.timeline-item.status-em_andamento:before {
    background: #ffab00;
}

.timeline-item.status-concluido:before {
    background: #71dd37;
}

.timeline-date {
    font-size: 0.875rem;
    color: #666;
    margin-bottom: 0.5rem;
}

.timeline-title {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.timeline-body {
    color: #4f4f4f;
}

.timeline-footer {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e0e0e0;
}
</style>
@endpush

@push('page-script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const historicoButtons = document.querySelectorAll('[data-bs-target="#modalHistorico"]');

    historicoButtons.forEach(button => {
        button.addEventListener('click', function() {
            const clienteId = this.getAttribute('data-cliente-id');
            const clienteNome = this.getAttribute('data-cliente-nome');

            // Atualiza o nome do cliente no modal
            document.getElementById('cliente_nome_historico').textContent = clienteNome;

            // Mostra loading
            document.getElementById('timeline_atendimentos').innerHTML = `
                <div class="d-flex justify-content-center p-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Carregando...</span>
                    </div>
                </div>
            `;

            // Busca os atendimentos
            fetch(`/customers/api/clientes/${clienteId}/atendimentos`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(response => {
                    if (!response.success) {
                        throw new Error(response.message);
                    }

                    const atendimentos = response.data;
                    let html = '';

                    if (atendimentos.length === 0) {
                        html = `
                            <div class="alert alert-info" role="alert">
                                <i class="bx bx-info-circle me-1"></i>
                                Nenhum atendimento encontrado para este cliente.
                            </div>
                        `;
                    } else {
                        atendimentos.forEach(atendimento => {
                            html += `
                                <div class="timeline-item border-start border-3 border-primary ps-3 mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge bg-primary">${atendimento.tipo_contato}</span>
                                        <small class="text-muted">${atendimento.created_at}</small>
                                    </div>

                                    <div class="mb-2">
                                        <strong>Status:</strong>
                                        <span class="badge bg-${getStatusBadgeColor(atendimento.status)}">
                                            ${atendimento.status}
                                        </span>
                                    </div>

                                    <p class="mb-2"><strong>Descrição:</strong> ${atendimento.descricao}</p>

                                    ${atendimento.retorno ?
                                        `<p class="mb-2"><strong>Retorno:</strong> ${atendimento.retorno}</p>` : ''}

                                    ${atendimento.data_retorno ?
                                        `<p class="mb-2"><strong>Data de Retorno:</strong> ${atendimento.data_retorno}</p>` : ''}

                                    ${atendimento.proxima_acao ?
                                        `<p class="mb-2"><strong>Próxima Ação:</strong> ${atendimento.proxima_acao}</p>` : ''}

                                    ${atendimento.data_proxima_acao ?
                                        `<p class="mb-2"><strong>Data da Próxima Ação:</strong> ${atendimento.data_proxima_acao}</p>` : ''}

                                    ${atendimento.ativar_lembrete ?
                                        `<p class="mb-2"><i class="bx bx-bell text-warning"></i> Lembrete ativado</p>` : ''}
                                </div>
                            `;
                        });
                    }

                    document.getElementById('timeline_atendimentos').innerHTML = html;
                })
                .catch(error => {
                    console.error('Erro:', error);
                    document.getElementById('timeline_atendimentos').innerHTML = `
                        <div class="alert alert-danger" role="alert">
                            <i class="bx bx-error-circle me-1"></i>
                            Erro ao carregar o histórico. Por favor, tente novamente.
                            <br>
                            <small class="mt-1 d-block">${error.message}</small>
                        </div>
                    `;
                });
        });
    });

    // Função auxiliar para determinar a cor do badge de status
    function getStatusBadgeColor(status) {
        switch (status.toLowerCase()) {
            case 'pendente':
                return 'warning';
            case 'concluído':
                return 'success';
            case 'cancelado':
                return 'danger';
            case 'em andamento':
                return 'info';
            default:
                return 'secondary';
        }
    }
});
</script>
@endpush