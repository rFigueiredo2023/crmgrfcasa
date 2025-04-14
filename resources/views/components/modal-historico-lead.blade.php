<!-- Modal de Histórico do Lead -->
<div class="modal fade" id="modalHistoricoLead" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2" id="modal-title">Histórico</h3>
                    <p class="text-muted">Visualize e adicione registros ao histórico</p>
                </div>

                <!-- Informações do Lead -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Razão Social</label>
                                    <p class="form-control-static" id="lead-razao-social"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">CNPJ</label>
                                    <p class="form-control-static" id="lead-cnpj"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Telefone</label>
                                    <p class="form-control-static" id="lead-telefone"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Contato</label>
                                    <p class="form-control-static" id="lead-contato"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Endereço</label>
                                    <p class="form-control-static" id="lead-endereco"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label fw-medium">Vendedora</label>
                                    <p class="form-control-static" id="lead-vendedora"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline do Histórico -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Linha do Tempo</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#novoHistoricoForm">
                            <i class="bx bx-plus me-1"></i> Novo Registro
                        </button>
                    </div>
                    <div class="card-body">
                        <!-- Formulário de Novo Histórico -->
                        <div class="collapse mb-4" id="novoHistoricoForm">
                            <div class="card">
                                <div class="card-body">
                                    <form id="formNovoHistorico">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">Tipo de Contato</label>
                                                <select class="form-select" name="tipo_contato" required>
                                                    <option value="">Selecione...</option>
                                                    <option value="Ligação">Ligação</option>
                                                    <option value="WhatsApp">WhatsApp</option>
                                                    <option value="E-mail">E-mail</option>
                                                    <option value="Visita">Visita</option>
                                                    <option value="Reunião">Reunião</option>
                                                    <option value="Outro">Outro</option>
                                                </select>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Descrição</label>
                                                <textarea class="form-control" name="descricao" rows="3" required></textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Próxima Ação</label>
                                                <textarea class="form-control" name="proxima_acao" rows="2"></textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Data da Próxima Ação</label>
                                                <input type="date" class="form-control" name="data_proxima_acao">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Retorno</label>
                                                <textarea class="form-control" name="retorno" rows="2"></textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Data de Retorno</label>
                                                <input type="date" class="form-control" name="data_retorno">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Anexo</label>
                                                <input type="file" class="form-control" name="anexo">
                                                <small class="text-muted">Arquivos permitidos: PDF, JPG, JPEG, PNG, GIF (máx. 5MB)</small>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="ativar_lembrete" id="ativar_lembrete">
                                                    <label class="form-check-label" for="ativar_lembrete">
                                                        Ativar lembrete
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-12 text-end">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#novoHistoricoForm">Cancelar</button>
                                                <button type="submit" class="btn btn-primary">Salvar</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline -->
                        <ul class="timeline" id="historico-timeline"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalHistorico = document.getElementById('modalHistoricoLead');
    const formNovoHistorico = document.getElementById('formNovoHistorico');
    let leadId = null;

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
    function carregarHistorico(id) {
        fetch(`/leads/${id}/historico`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Erro ao carregar histórico');
                }
                return response.json();
            })
            .then(data => {
                // Atualizar título do modal
                document.getElementById('modal-title').textContent = `Histórico de: ${data.cliente.razao_social}`;

                // Atualizar informações do lead
                document.getElementById('lead-razao-social').textContent = data.cliente.razao_social;
                document.getElementById('lead-cnpj').textContent = data.cliente.cnpj;
                document.getElementById('lead-telefone').textContent = data.cliente.telefone;
                document.getElementById('lead-contato').textContent = data.cliente.contato;
                document.getElementById('lead-endereco').textContent = data.cliente.endereco;
                document.getElementById('lead-vendedora').textContent = data.cliente.vendedora;

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

                // Função para obter a classe do badge baseado no tipo
                function getTipoBadgeClass(tipo) {
                    const classes = {
                        'Ligação': 'bg-label-primary',
                        'WhatsApp': 'bg-label-success',
                        'E-mail': 'bg-label-info',
                        'Visita': 'bg-label-warning',
                        'Reunião': 'bg-label-danger',
                        'Outro': 'bg-label-secondary'
                    };
                    return classes[tipo] || 'bg-label-secondary';
                }

                data.historicos.forEach(historico => {
                    const li = document.createElement('li');
                    li.className = 'timeline-item';

                    li.innerHTML = `
                        <div class="timeline-indicator">
                            <i class="bx ${getTipoIcon(historico.tipo)}"></i>
                        </div>
                        <div class="timeline-event">
                            <div class="timeline-header mb-1">
                                <h6 class="mb-0">
                                    <span class="badge ${getTipoBadgeClass(historico.tipo)}">${historico.tipo}</span>
                                </h6>
                                <small class="text-muted">${historico.data}</small>
                            </div>
                            <p class="event-description mb-2">${historico.texto}</p>
                            ${historico.proxima_acao ? `
                                <div class="event-next-action">
                                    <small class="text-muted">Próxima Ação:</small>
                                    <p class="mb-0">${historico.proxima_acao}</p>
                                    ${historico.data_proxima_acao ? `<small class="text-muted">Data: ${historico.data_proxima_acao}</small>` : ''}
                                </div>
                            ` : ''}
                            ${historico.retorno ? `
                                <div class="event-return">
                                    <small class="text-muted">Retorno:</small>
                                    <p class="mb-0">${historico.retorno}</p>
                                    ${historico.data_retorno ? `<small class="text-muted">Data: ${historico.data_retorno}</small>` : ''}
                                </div>
                            ` : ''}
                            ${historico.anexo ? `
                                <div class="mt-2">
                                    <a href="${historico.anexo}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bx bx-paperclip me-1"></i>Ver Anexo
                                    </a>
                                </div>
                            ` : ''}
                        </div>
                    `;

                    timeline.appendChild(li);
                });
            })
            .catch(error => {
                console.error('Erro:', error);
                showToast('Erro ao carregar histórico', 'error');
            });
    }

    // Evento de abertura do modal
    modalHistorico.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        leadId = button.getAttribute('data-lead-id');
        carregarHistorico(leadId);
    });

    // Submit do formulário de novo histórico
    formNovoHistorico.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        fetch(`/leads/${leadId}/historico`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Histórico registrado com sucesso');
                this.reset();
                document.querySelector('#novoHistoricoForm').classList.remove('show');
                carregarHistorico(leadId);
            } else {
                throw new Error(data.message || 'Erro ao registrar histórico');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            showToast(error.message || 'Erro ao registrar histórico', 'error');
        });
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
    padding-bottom: 1.5rem;
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

.badge.bg-label-success {
    background-color: #e8fadf !important;
    color: #71dd37 !important;
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

.badge.bg-label-secondary {
    background-color: #ebeef0 !important;
    color: #8592a3 !important;
    font-size: 0.85em;
}
</style>
@endpush