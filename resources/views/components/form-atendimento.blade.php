{{-- Componente Form atendimento --}}
@push('modals')
<!-- Modal de Novo Atendimento -->
<div class="modal fade" id="modalAtendimento" tabindex="-1" aria-labelledby="modalAtendimentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAtendimentoLabel">Novo Atendimento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formAtendimento" action="{{ route('atendimentos.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="cliente_id" id="cliente_id">
                    <input type="hidden" name="data_atendimento" value="{{ now() }}">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Cliente</label>
                            <input type="text" class="form-control" id="cliente_nome" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="">Selecione o status</option>
                                @foreach (App\Enums\StatusAtendimento::cases() as $status)
                                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Contato</label>
                            <select class="form-select" name="tipo_contato" required>
                                <option value="">Selecione o tipo...</option>
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
                            <textarea class="form-control" name="descricao" rows="4" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">
                                <i class="bx bx-message-square-dots me-1"></i> Retorno
                            </label>
                            <div class="row">
                                <div class="col-md-8">
                                    <textarea class="form-control" name="retorno" rows="2"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <input type="date" class="form-control" name="data_retorno">
                                </div>
                            </div>
                            <div class="form-text">Registre o retorno do cliente e a data</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">
                                <i class="bx bx-calendar-exclamation me-1"></i> Próxima Ação
                            </label>
                            <div class="row">
                                <div class="col-md-8">
                                    <textarea class="form-control" name="proxima_acao" rows="2"></textarea>
                                </div>
                                <div class="col-md-4">
                                    <input type="date" class="form-control" name="data_proxima_acao">
                                </div>
                            </div>
                            <div class="form-text">Defina a próxima ação e sua data prevista</div>
                        </div>
                        <div class="col-12">
                            <div class="form-check mb-3">
                                <input type="hidden" name="ativar_lembrete" value="0">
                                <input class="form-check-input" type="checkbox" name="ativar_lembrete" value="1" id="ativar_lembrete">
                                <label class="form-check-label" for="ativar_lembrete">
                                    <i class="bx bx-bell me-1"></i> Ativar lembrete
                                </label>
                                <div class="form-text">Receba notificações quando a data prevista chegar</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">
                                <i class="bx bx-paperclip me-1"></i> Anexo
                            </label>
                            <input type="file" class="form-control" name="anexo" accept=".pdf,.jpg,.jpeg,.png,.gif">
                            <div class="form-text">Arquivos permitidos: PDF, JPG, PNG, GIF (máx. 5MB)</div>
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
</div>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('#formAtendimento');
    const modal = document.getElementById('modalAtendimento');

    // Registrar log para verificar se o modal existe
    console.log('Modal de Atendimento encontrado:', modal ? 'Sim' : 'Não');

    // Inicializar modal com opções que podem ajudar com problemas de foco
    let modalInstance;
    try {
        modalInstance = new bootstrap.Modal(modal, {
            backdrop: true,
            keyboard: true,
            focus: true
        });
        console.log('Modal inicializado com sucesso');
    } catch (e) {
        console.error('Erro ao inicializar modal:', e);
    }

    // Eventos para debugging de visibilidade
    modal.addEventListener('show.bs.modal', function(event) {
        console.log('Evento show.bs.modal acionado');

        const button = event.relatedTarget;

        // Tenta obter valores para cliente ou lead
        let id, nome, tipo = 'cliente';

        if (button) {
            id = button.getAttribute('data-cliente-id') || button.getAttribute('data-id');
            nome = button.getAttribute('data-cliente-nome') || button.getAttribute('data-nome');

            if (button.getAttribute('data-tipo') === 'lead' || button.getAttribute('data-lead-id')) {
                tipo = 'lead';
            }
        } else {
            // Caso o modal seja aberto via jQuery
            id = document.getElementById('cliente_id').value;
            nome = document.getElementById('cliente_nome').value;
        }

        // Verifica se os dados existem
        if (!id || !nome) {
            console.error('ID ou nome não encontrados');
            return;
        }

        document.getElementById('cliente_id').value = id;
        document.getElementById('cliente_nome').value = nome;

        // Armazena o tipo para uso no envio do formulário
        form.setAttribute('data-tipo', tipo);

        console.log('Configuração do modal concluída - ID:', id, 'Nome:', nome, 'Tipo:', tipo);
    });

    // Evento para debugging quando o modal é realmente exibido
    modal.addEventListener('shown.bs.modal', function() {
        console.log('Modal completamente visível (shown.bs.modal)');
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);
        const submitButton = form.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        const tipo = form.getAttribute('data-tipo') || 'cliente';
        const id = document.getElementById('cliente_id').value;

        // Determina a URL com base no tipo (cliente ou lead)
        let url;
        if (tipo === 'lead') {
            url = `/leads/${id}/atendimentos`;
            console.log('URL para lead:', url);
        } else {
            url = form.action; // Usa a URL padrão para clientes
            console.log('URL para cliente:', url);
        }

        // Log para depuração
        console.log(`Enviando formulário como tipo: ${tipo}, ID: ${id}, URL: ${url}`);

        // Desabilita o botão e mostra loading
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Salvando...';

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Sucesso
                if (modalInstance) {
                    modalInstance.hide();
                } else if (typeof jQuery !== 'undefined') {
                    jQuery('#modalAtendimento').modal('hide');
                }
                form.reset();

                // Mostra mensagem de sucesso
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    // Recarrega a página após fechar o alerta
                    window.location.reload();
                });
            } else {
                throw new Error(data.message || 'Erro ao salvar atendimento');
            }
        })
        .catch(error => {
            // Mostra mensagem de erro
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: error.message,
                confirmButtonText: 'Ok'
            });
        })
        .finally(() => {
            // Restaura o botão
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        });
    });
});
</script>
@endpush
