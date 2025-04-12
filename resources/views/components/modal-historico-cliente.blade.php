<!-- Modal de Histórico do Cliente -->
<div class="modal fade" id="modalHistoricoCliente" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-simple">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Histórico do Cliente</h3>
                    <p class="text-muted">Visualize e adicione registros ao histórico</p>
                </div>

                <!-- Informações do Cliente -->
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Razão Social</label>
                                    <p class="form-control-static" id="cliente-razao-social"></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">CNPJ</label>
                                    <p class="form-control-static" id="cliente-cnpj"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Timeline de Históricos -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Linha do Tempo</h5>
                    </div>
                    <div class="card-body">
                        <div class="timeline" id="historico-timeline">
                            <!-- Os históricos serão carregados aqui via AJAX -->
                        </div>
                    </div>
                </div>

                <!-- Formulário de Novo Histórico -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Novo Registro</h5>
                    </div>
                    <div class="card-body">
                        <form id="formNovoHistorico" class="row g-3">
                            @csrf
                            <input type="hidden" name="cliente_id" id="cliente_id">
                            
                            <div class="col-12">
                                <label class="form-label" for="descricao">Descrição</label>
                                <textarea class="form-control" id="descricao" name="texto" rows="3" required></textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="proxima_acao">
                                    <i class="bx bx-calendar-exclamation me-1"></i> Próxima Ação
                                </label>
                                <textarea class="form-control" id="proxima_acao" name="proxima_acao" rows="2" placeholder="Opcional"></textarea>
                            </div>

                            <div class="col-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Salvar Histórico
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const formNovoHistorico = document.getElementById('formNovoHistorico');
    const modalHistorico = document.getElementById('modalHistoricoCliente');
    const clienteIdInput = document.getElementById('cliente_id');

    // Função para carregar o histórico
    function carregarHistorico(clienteId) {
        fetch(`/clientes/${clienteId}/historico`)
            .then(response => response.json())
            .then(data => {
                // Atualizar informações do cliente
                document.getElementById('cliente-razao-social').textContent = data.cliente.razao_social;
                document.getElementById('cliente-cnpj').textContent = data.cliente.cnpj;

                // Atualizar timeline
                const timeline = document.getElementById('historico-timeline');
                timeline.innerHTML = '';

                data.historicos.forEach(historico => {
                    const item = document.createElement('div');
                    item.className = 'timeline-item timeline-item-primary pb-4';
                    item.innerHTML = `
                        <span class="timeline-indicator timeline-indicator-primary">
                            <i class="bx bx-user"></i>
                        </span>
                        <div class="timeline-event">
                            <div class="timeline-header border-bottom mb-3">
                                <h6 class="mb-0">${historico.vendedora}</h6>
                                <small class="text-muted">${historico.data}</small>
                            </div>
                            <div class="d-flex flex-column gap-2">
                                <div class="event-description">
                                    ${historico.texto}
                                </div>
                                ${historico.proxima_acao ? `
                                    <div class="event-next-action d-flex align-items-center">
                                        <i class="bx bx-calendar-exclamation text-primary me-2"></i>
                                        <span class="text-muted fst-italic">${historico.proxima_acao}</span>
                                    </div>
                                ` : ''}
                            </div>
                        </div>
                    `;
                    timeline.appendChild(item);
                });
            })
            .catch(error => console.error('Erro ao carregar histórico:', error));
    }

    // Evento de submissão do formulário
    formNovoHistorico.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const clienteId = clienteIdInput.value;
        const formData = new FormData(this);

        fetch(`/clientes/${clienteId}/historico`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Limpar o formulário
                this.reset();
                
                // Recarregar o histórico
                carregarHistorico(clienteId);

                // Mostrar mensagem de sucesso
                const toast = document.createElement('div');
                toast.className = 'bs-toast toast toast-placement-ex m-2 fade bg-success top-0 end-0 show';
                toast.setAttribute('role', 'alert');
                toast.setAttribute('aria-live', 'assertive');
                toast.setAttribute('aria-atomic', 'true');
                toast.innerHTML = `
                    <div class="toast-header">
                        <i class="bx bx-check me-2"></i>
                        <div class="me-auto fw-semibold">Sucesso!</div>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        Histórico registrado com sucesso!
                    </div>
                `;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            }
        })
        .catch(error => console.error('Erro ao salvar histórico:', error));
    });

    // Evento de abertura do modal
    modalHistorico.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const clienteId = button.getAttribute('data-cliente-id');
        clienteIdInput.value = clienteId;
        carregarHistorico(clienteId);
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

.event-next-action {
    border-top: 1px solid #d9dee3;
    padding-top: 0.75rem;
    margin-top: 0.75rem;
}
</style>
@endpush 