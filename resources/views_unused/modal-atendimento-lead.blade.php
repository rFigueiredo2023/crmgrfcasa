{{-- Componente Modal atendimento lead --}}
<!-- Modal -->
<div class="modal fade" id="modalAtendimentoLead" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Atendimento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formAtendimentoLead" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tipo_contato" class="form-label">Tipo de Contato</label>
                            <select class="form-select" id="tipo" name="tipo" required>
                                <option value="">Selecione...</option>
                                <option value="Ligação">Ligação</option>
                                <option value="WhatsApp">WhatsApp</option>
                                <option value="E-mail">E-mail</option>
                                <option value="Visita">Visita</option>
                                <option value="Reunião">Reunião</option>
                                <option value="Outro">Outro</option>
                            </select>
                            <div class="invalid-feedback">Por favor, selecione um tipo de contato.</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status do Lead</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Selecione...</option>
                                <option value="Frio">Frio</option>
                                <option value="Morno">Morno</option>
                                <option value="Quente">Quente</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="texto" class="form-label">Descrição</label>
                        <textarea class="form-control" id="texto" name="texto" rows="3" required></textarea>
                        <div class="invalid-feedback">Por favor, informe a descrição do atendimento.</div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="proxima_acao" class="form-label">Próxima Ação</label>
                            <textarea class="form-control" id="proxima_acao" name="proxima_acao" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="data_proxima_acao" class="form-label">Data da Próxima Ação</label>
                            <input type="datetime-local" class="form-control" id="data_proxima_acao" name="data_proxima_acao">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="retorno" class="form-label">Retorno</label>
                            <textarea class="form-control" id="retorno" name="retorno" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="data_retorno" class="form-label">Data de Retorno</label>
                            <input type="datetime-local" class="form-control" id="data_retorno" name="data_retorno">
                        </div>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="ativar_lembrete" name="ativar_lembrete" value="1">
                        <label class="form-check-label" for="ativar_lembrete">Ativar lembrete</label>
                    </div>
                    <div class="mb-3">
                        <label for="anexo" class="form-label">Anexo</label>
                        <input class="form-control" type="file" id="anexo" name="anexo">
                        <div class="form-text">Arquivos permitidos: pdf, jpg, jpeg, png (máx: 5MB)</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formAtendimentoLead = document.getElementById('formAtendimentoLead');

    formAtendimentoLead.addEventListener('submit', function(e) {
        e.preventDefault();

        // Validar campos obrigatórios
        let isValid = true;
        const tipo = document.getElementById('tipo');
        const texto = document.getElementById('texto');

        if (!tipo.value) {
            tipo.classList.add('is-invalid');
            isValid = false;
        } else {
            tipo.classList.remove('is-invalid');
        }

        if (!texto.value) {
            texto.classList.add('is-invalid');
            isValid = false;
        } else {
            texto.classList.remove('is-invalid');
        }

        if (!isValid) return;

        const leadId = this.getAttribute('data-lead-id');
        this.action = `/leads/${leadId}/historico`;

        const formData = new FormData(this);

        // Ajustar valores do formulário conforme necessário
        if (formData.get('data_proxima_acao') === '') {
            formData.delete('data_proxima_acao');
        }

        if (formData.get('data_retorno') === '') {
            formData.delete('data_retorno');
        }

        // Enviar requisição
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Erro ao processar requisição');
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Fechar o modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalAtendimentoLead'));
                modal.hide();

                // Limpar o formulário
                formAtendimentoLead.reset();

                // Atualizar a timeline (se existir)
                if (typeof updateHistorico === 'function') {
                    updateHistorico(leadId);
                }

                // Mostrar mensagem de sucesso
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: data.message || 'Atendimento registrado com sucesso.',
                    customClass: {
                        confirmButton: 'btn btn-success'
                    }
                });
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: error.message || 'Ocorreu um erro ao registrar o atendimento.',
                customClass: {
                    confirmButton: 'btn btn-danger'
                }
            });
        });
    });
});
</script>
