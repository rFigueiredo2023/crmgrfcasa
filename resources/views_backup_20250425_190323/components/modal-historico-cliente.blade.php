{{-- Componente Modal historico cliente --}}
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
    const modal = new bootstrap.Modal(document.getElementById('modalHistorico'));

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

            if (data.success === false) {
                throw new Error(data.message || 'Erro ao carregar os dados.');
            }

            // Limpa a tabela
            tabelaHistorico.innerHTML = '';

            // Verifica se há dados para exibir
            if (data.historicos && data.historicos.length > 0) {
                // Renderiza o histórico
                data.historicos.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${item.tipo || 'N/A'}</td>
                        <td>${item.texto || 'N/A'}</td>
                        <td><span class="badge bg-label-info">${item.data || 'N/A'}</span></td>
                        <td>${item.vendedora || 'N/A'}</td>
                    `;
                    tabelaHistorico.appendChild(row);
                });
            } else if (data.data && data.data.length > 0) {
                // Formato alternativo para leads
                data.data.forEach(item => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${item.tipo || 'N/A'}</td>
                        <td>${item.texto || 'N/A'}</td>
                        <td><span class="badge bg-label-info">${item.data || 'N/A'}</span></td>
                        <td>${item.user?.name || 'N/A'}</td>
                    `;
                    tabelaHistorico.appendChild(row);
                });
            } else {
                // Nenhum histórico
                const row = document.createElement('tr');
                row.innerHTML = `<td colspan="4" class="text-center">Nenhum histórico encontrado</td>`;
                tabelaHistorico.appendChild(row);
            }

            // Exibe o modal
            modal.show();
        } catch (error) {
            console.error('Erro ao carregar histórico:', error);
            setLoading(false);

            Swal.fire({
                icon: 'error',
                title: 'Erro',
                text: error.message || 'Erro ao carregar o histórico de atendimentos.'
            });
        }
    }

    // Inicializa o modal
    const modalHistorico = document.getElementById('modalHistorico');
    if (modalHistorico) {
        const modal = new bootstrap.Modal(modalHistorico);

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
                    : `/customers/api/clientes/${id}/atendimentos`;

                carregarHistorico(id, nome, url);
            });
        });
    }
});
</script>
@endpush
