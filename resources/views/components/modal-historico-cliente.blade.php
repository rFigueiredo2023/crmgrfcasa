<!-- Modal de Histórico -->
<div class="modal fade" id="modalHistoricoCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2" id="modal-title">Histórico</h3>
                    <p class="text-muted">Visualize e adicione registros ao histórico</p>
                </div>

                <!-- Informações do Cliente/Lead -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Razão Social</label>
                                    <p class="form-control-static" id="cliente-razao-social"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">CNPJ</label>
                                    <p class="form-control-static" id="cliente-cnpj"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Telefone</label>
                                    <p class="form-control-static" id="cliente-telefone"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Contato</label>
                                    <p class="form-control-static" id="cliente-contato"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Endereço</label>
                                    <p class="form-control-static" id="cliente-endereco"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Vendedora Responsável</label>
                                    <p class="form-control-static" id="cliente-vendedora"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline de Históricos -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Linha do Tempo</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline" id="historico-timeline">
                            <!-- Os históricos serão carregados aqui via AJAX -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalHistorico = document.getElementById('modalHistoricoCliente');

    // Função para mostrar toast
    function showToast(message, type = 'success') {
        Swal.fire({
            text: message,
            icon: type,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    }

    // Função para carregar o histórico
    function carregarHistorico(id, tipo) {
        const url = tipo === 'lead' ? `/leads/${id}/historico` : `/clientes/${id}/historico`;
        
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao carregar histórico');
                }
                return response.json();
            })
            .then(data => {
                // Atualizar título do modal
                document.getElementById('modal-title').textContent = `Histórico de: ${data.cliente.razao_social}`;

                // Atualizar informações do cliente/lead
                document.getElementById('cliente-razao-social').textContent = data.cliente.razao_social || 'Não informado';
                document.getElementById('cliente-cnpj').textContent = data.cliente.cnpj || 'Não informado';
                document.getElementById('cliente-telefone').textContent = data.cliente.telefone || 'Não informado';
                document.getElementById('cliente-contato').textContent = data.cliente.contato || 'Não informado';
                document.getElementById('cliente-endereco').textContent = data.cliente.endereco || 'Não informado';
                document.getElementById('cliente-vendedora').textContent = data.cliente.vendedora || 'Não atribuído';

                // Atualizar timeline
                const timeline = document.getElementById('historico-timeline');
                timeline.innerHTML = '';

                // Função para obter o ícone baseado no tipo
                function getTipoIcon(tipo) {
                    const icons = {
                        'Ligação': 'bx-phone-call',
                        'WhatsApp': 'bxl-whatsapp',
                        'E-mail': 'bx-envelope',
                        'Visita': 'bx-walk',
                        'Reunião': 'bx-group',
                        'Outro': 'bx-message-square-detail'
                    };
                    return icons[tipo] || 'bx-message-square-detail';
                }

                data.historicos.forEach(historico => {
                    const item = document.createElement('div');
                    item.className = 'timeline-item timeline-item-primary pb-4';
                    
                    // Verificar se tem ação vencida
                    const isAcaoVencida = historico.data_proxima_acao && new Date(historico.data_proxima_acao) < new Date();
                    const isRetornoVencido = historico.data_retorno && new Date(historico.data_retorno) < new Date();
                    
                    item.innerHTML = `
                        <span class="timeline-indicator timeline-indicator-primary">
                            <i class="bx ${getTipoIcon(historico.tipo)}"></i>
                        </span>
                        <div class="timeline-event">
                            <div class="timeline-header border-bottom mb-3">
                                <div class="d-flex flex-column">
                                    <h6 class="mb-0">${historico.vendedora}</h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-label-primary">${historico.tipo}</span>
                                        <small class="text-muted">${historico.data}</small>
                                        ${historico.ativar_lembrete ? '<i class="bx bx-bell text-warning" title="Lembrete ativo"></i>' : ''}
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex flex-column gap-2">
                                <div class="event-description">
                                    ${historico.texto}
                                </div>
                                ${historico.retorno ? `
                                    <div class="event-return">
                                        <div class="d-flex align-items-center">
                                            <i class="bx bx-message-square-dots ${isRetornoVencido ? 'text-danger' : 'text-info'} me-2"></i>
                                            <span class="badge ${isRetornoVencido ? 'bg-label-danger' : 'bg-label-info'}">
                                                Retorno ${historico.data_retorno ? `(${historico.data_retorno})` : ''}
                                            </span>
                                        </div>
                                        <div class="mt-2 text-muted">
                                            ${historico.retorno}
                                        </div>
                                    </div>
                                ` : ''}
                                ${historico.proxima_acao ? `
                                    <div class="event-next-action">
                                        <div class="d-flex align-items-center">
                                            <i class="bx bx-calendar-exclamation ${isAcaoVencida ? 'text-danger' : 'text-warning'} me-2"></i>
                                            <span class="badge ${isAcaoVencida ? 'bg-label-danger' : 'bg-label-warning'}">
                                                Próxima Ação ${historico.data_proxima_acao ? `(${historico.data_proxima_acao})` : ''}
                                            </span>
                                        </div>
                                        <div class="mt-2 text-muted">
                                            ${historico.proxima_acao}
                                        </div>
                                    </div>
                                ` : ''}
                                ${historico.anexo ? `
                                    <div class="event-attachment mt-2">
                                        <a href="${historico.anexo}" target="_blank" class="d-flex align-items-center">
                                            <i class="bx bx-paperclip text-primary me-2"></i>
                                            <span>Anexo</span>
                                        </a>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                    timeline.appendChild(item);
                });
            })
            .catch(error => {
                console.error('Erro ao carregar histórico:', error);
                showToast('Erro ao carregar histórico', 'error');
            });
    }

    // Evento de abertura do modal
    modalHistorico.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const id = button.getAttribute('data-cliente-id') || button.getAttribute('data-lead-id');
        const tipo = button.hasAttribute('data-lead-id') ? 'lead' : 'cliente';
        
        carregarHistorico(id, tipo);
    });
});
</script>
@endpush

@push('styles')
<style>
.timeline {
    margin: 0;
    padding: 0;
    list-style: none;
}

.timeline-item {
    position: relative;
    padding-left: 3rem;
}

.timeline-item:not(:last-child) {
    border-left: 1px solid #d9dee3;
    margin-left: 1.2rem;
}

.timeline-indicator {
    position: absolute;
    left: 0;
    top: 0;
    width: 2.5rem;
    height: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border: 2px solid #696cff;
    border-radius: 50%;
    margin-left: -1.2rem;
}

.timeline-indicator i {
    color: #696cff;
    font-size: 1.2rem;
}

.timeline-event {
    background: #fff;
    border-radius: 0.375rem;
    padding: 1.25rem;
    margin-bottom: 1.25rem;
    box-shadow: 0 0.25rem 1rem rgba(161, 172, 184, 0.12);
}

.timeline-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.event-description {
    color: #697a8d;
    white-space: pre-line;
}

.event-next-action,
.event-return {
    border-top: 1px solid #d9dee3;
    padding-top: 0.75rem;
    margin-top: 0.75rem;
}

.badge.bg-label-primary {
    background-color: #e7e7ff !important;
    color: #696cff !important;
    font-size: 0.85em;
}

.badge.bg-label-warning {
    background-color: #fff2d6 !important;
    color: #ffab00 !important;
    font-size: 0.85em;
}

.badge.bg-label-danger {
    background-color: #ffe0db !important;
    color: #ff3e1d !important;
    font-size: 0.85em;
}

.badge.bg-label-info {
    background-color: #d7f5fc !important;
    color: #03c3ec !important;
    font-size: 0.85em;
}

.event-attachment a {
    color: #697a8d;
    text-decoration: none;
}

.event-attachment a:hover {
    color: #696cff;
}

.form-label.fw-medium {
    font-weight: 500;
    color: #566a7f;
}
</style>
@endpush 