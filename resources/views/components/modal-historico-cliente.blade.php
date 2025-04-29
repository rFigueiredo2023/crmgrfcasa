{{-- Componente Modal historico cliente --}}
<!-- Modal de Histórico -->
<div class="modal fade" id="modalHistorico" tabindex="-1" aria-labelledby="modalHistoricoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalHistoricoLabel">Histórico de Atendimentos</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeModalHistorico"></button>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="closeModalHistoricoFooter">Fechar</button>
            </div>
        </div>
    </div>
</div>

@push('page-style')
<style>
.timeline-wrapper {
    position: relative;
    padding: 1rem;
}

.timeline-list {
    position: relative;
    padding-left: 2.5rem;
}

.timeline-list::before {
    content: '';
    position: absolute;
    left: 0.75rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    padding-bottom: 1.5rem;
}

.timeline-point {
    position: absolute;
    left: -2.19rem;
    width: 1rem;
    height: 1rem;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-content {
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 0.375rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.timeline-content h6 {
    color: #495057;
    font-weight: 600;
}

.timeline-content p {
    color: #6c757d;
    margin-bottom: 0.5rem;
}

.timeline-content .badge {
    font-size: 0.75rem;
    padding: 0.35em 0.65em;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos do modal
    const timelineContainer = document.getElementById('timeline_atendimentos');
    const clienteNomeElement = document.getElementById('cliente_nome_historico');
    const tituloHistorico = document.getElementById('modalHistoricoLabel');
    const tabelaHistorico = document.getElementById('timeline_atendimentos');
    const modalElement = document.getElementById('modalHistorico');
    const modal = modalElement ? new bootstrap.Modal(modalElement) : null;

    // Garantir que o backdrop seja removido quando o modal for fechado
    if (modalElement) {
        modalElement.addEventListener('hidden.bs.modal', function() {
            // Remove qualquer backdrop restante
            const backdrops = document.getElementsByClassName('modal-backdrop');
            while(backdrops[0]) {
                backdrops[0].parentNode.removeChild(backdrops[0]);
            }
            // Remover classe modal-open do body
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    }

    // Função para exibir/ocultar o indicador de carregamento
    function setLoading(loading) {
        const loadingIndicator = document.querySelector('#modalHistorico .loading-indicator');
        if (loadingIndicator) {
            loadingIndicator.style.display = loading ? 'flex' : 'none';
        }

        // Alternativa se não existir um indicador de carregamento específico
        if (tabelaHistorico) {
            if (loading) {
                tabelaHistorico.innerHTML = '<tr><td colspan="4" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Carregando...</span></div></td></tr>';
            }
        }
    }

    // Função para formatar a data
    function formatarData(dataString) {
        const data = new Date(dataString);
        return data.toLocaleDateString('pt-BR') + ' às ' +
               data.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    }

    // Função para carregar os dados do histórico
    async function carregarHistorico(id, nome, url) {
        const timelineContainer = document.getElementById('timeline_atendimentos');
        const clienteNomeElement = document.getElementById('cliente_nome_historico');
        const tituloHistorico = document.getElementById('modalHistoricoLabel');
        const tabelaHistorico = document.getElementById('timeline_atendimentos');
        const modal = new bootstrap.Modal(document.getElementById('modalHistorico'));

        if (clienteNomeElement) {
            clienteNomeElement.textContent = nome;
        }

        if (timelineContainer) {
            timelineContainer.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"><span class="visually-hidden">Carregando...</span></div></div>';
        }

        try {
            setLoading(true);
            console.log('Carregando histórico da URL:', url);

            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status}`);
            }

            const data = await response.json();
            setLoading(false);
            console.log('Dados recebidos da API:', data);

            // Limpa o container
            timelineContainer.innerHTML = '';

            // Verifica o formato dos dados (diferentes APIs podem retornar formatos diferentes)
            let atendimentosArray = [];

            if (Array.isArray(data)) {
                // Resposta direta como array
                atendimentosArray = data;
            } else if (data.success === false) {
                throw new Error(data.message || 'Erro ao carregar os dados.');
            } else if (data.historicos && Array.isArray(data.historicos)) {
                // Formato com historicos
                atendimentosArray = data.historicos;
            } else if (data.data && Array.isArray(data.data)) {
                // Formato com data
                atendimentosArray = data.data;
            } else {
                // Sem dados
                atendimentosArray = [];
            }

            // Se não houver atendimentos
            if (atendimentosArray.length === 0) {
                timelineContainer.innerHTML = `
                    <div class="alert alert-info">
                        Nenhum histórico de atendimento encontrado para este cliente.
                    </div>
                `;
                modal.show();
                return;
            }

            // Renderiza os atendimentos na timeline
            atendimentosArray.forEach(item => {
                // Data do atendimento (pode estar em diferentes campos dependendo da API)
                const dataAtendimento = item.data_atendimento || item.data || item.created_at || 'N/A';

                // Determina a cor com base no status
                let statusColor = 'primary';
                if (item.status) {
                    if (item.status.toLowerCase() === 'concluído' || item.status.toLowerCase() === 'concluido') {
                        statusColor = 'success';
                    } else if (item.status.toLowerCase() === 'pendente') {
                        statusColor = 'warning';
                    } else if (item.status.toLowerCase() === 'cancelado') {
                        statusColor = 'danger';
                    }
                }

                // Cria o item da timeline
                const timelineItem = document.createElement('div');
                timelineItem.className = 'timeline-item';
                timelineItem.innerHTML = `
                    <div class="timeline-point bg-${statusColor}"></div>
                    <div class="timeline-content">
                        <div class="d-flex justify-content-between mb-2">
                            <h6>${item.tipo_contato || 'Atendimento'}</h6>
                            <span class="badge bg-label-${statusColor}">${dataAtendimento}</span>
                        </div>
                        <p>${item.descricao}</p>
                        ${item.proxima_acao ? `<p><strong>Próxima ação:</strong> ${item.proxima_acao}</p>` : ''}
                        ${item.data_proxima_acao ? `<p><strong>Data próxima ação:</strong> ${item.data_proxima_acao}</p>` : ''}
                        <div class="text-end mt-2">
                            <small class="text-muted">Atendente: ${item.vendedor || 'Não atribuído'}</small>
                        </div>
                    </div>
                `;
                timelineContainer.appendChild(timelineItem);
            });

            // Exibe o modal
            modal.show();
        } catch (error) {
            console.error('Erro ao carregar histórico:', error);
            setLoading(false);

            // Exibe mensagem de erro no container
            timelineContainer.innerHTML = `
                <div class="alert alert-danger">
                    Erro ao carregar histórico: ${error.message}
                </div>
            `;

            modal.show();
        }
    }

    // Inicializa o modal
    const modalHistorico = document.getElementById('modalHistorico');
    if (modalHistorico) {
        const modal = new bootstrap.Modal(modalHistorico);

        // Função para fechar o modal e limpar o backdrop
        function fecharModalCompletamente() {
            modal.hide();
            // Garantir que o backdrop seja removido
            setTimeout(() => {
                const backdrops = document.getElementsByClassName('modal-backdrop');
                while(backdrops[0]) {
                    backdrops[0].parentNode.removeChild(backdrops[0]);
                }
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }, 150);
        }

        // Manipuladores para os botões de fechar
        const closeBtnHeader = document.getElementById('closeModalHistorico');
        const closeBtnFooter = document.getElementById('closeModalHistoricoFooter');

        if (closeBtnHeader) {
            closeBtnHeader.addEventListener('click', fecharModalCompletamente);
        }

        if (closeBtnFooter) {
            closeBtnFooter.addEventListener('click', fecharModalCompletamente);
        }

        // Adiciona o evento de click aos botões
        document.querySelectorAll('[data-bs-target="#modalHistorico"]').forEach(button => {
            button.addEventListener('click', function() {
                // Tenta pegar os dados no formato para cliente
                let id = this.getAttribute('data-cliente-id');
                let nome = this.getAttribute('data-cliente-nome');
                let tipo = 'cliente';

                // Se não encontrou, tenta no formato para lead
                if (!id || !nome) {
                    id = this.getAttribute('data-id');
                    nome = this.getAttribute('data-nome');
                    tipo = this.getAttribute('data-tipo') || 'cliente';
                }

                // Verificação adicional para diferenciar leads de clientes
                // Se o botão tiver a classe lead-button ou estiver na lista de leads
                if (this.classList.contains('lead-button') ||
                    this.closest('.leads-table') ||
                    this.getAttribute('data-tipo') === 'lead' ||
                    window.location.href.includes('leads')) {
                    tipo = 'lead';
                }

                if (!id || !nome) {
                    console.error('Dados do cliente/lead não encontrados no botão');
                    return;
                }

                console.log(`Carregando histórico para ${tipo} ID:`, id, 'Nome:', nome);

                // Determina a URL correta com base no tipo
                let url = tipo === 'lead'
                    ? `/lead-historico/${id}`
                    : `/historico/cliente/${id}`;

                console.log(`URL para histórico: ${url}`);
                carregarHistorico(id, nome, url);
            });
        });
    }
});
</script>
@endpush
