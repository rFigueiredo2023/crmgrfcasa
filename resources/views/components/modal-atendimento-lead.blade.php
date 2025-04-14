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
                            <select class="form-select" id="tipo_contato" name="tipo_contato" required>
                                <option value="">Selecione...</option>
                                <option value="Ligação">Ligação</option>
                                <option value="WhatsApp">WhatsApp</option>
                                <option value="E-mail">E-mail</option>
                                <option value="Visita">Visita</option>
                                <option value="Reunião">Reunião</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status do Lead</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Selecione...</option>
                                <option value="Frio">Frio</option>
                                <option value="Morno">Morno</option>
                                <option value="Quente">Quente</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="proxima_acao" class="form-label">Próxima Ação</label>
                        <textarea class="form-control" id="proxima_acao" name="proxima_acao" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="anexo" class="form-label">Anexo</label>
                        <input class="form-control" type="file" id="anexo" name="anexo">
                        <div class="form-text">Arquivos permitidos: pdf, doc, docx, xls, xlsx, jpg, jpeg, png</div>
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

        const leadId = this.getAttribute('data-lead-id');
        this.action = `/leads/${leadId}/atendimento`;

        const formData = new FormData(this);

        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Fechar o modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('modalAtendimentoLead'));
                modal.hide();

                // Limpar o formulário
                formAtendimentoLead.reset();

                // Atualizar a timeline (se existir)
                if (typeof updateTimeline === 'function') {
                    updateTimeline();
                }

                // Mostrar mensagem de sucesso
                Swal.fire({
                    icon: 'success',
                    title: 'Sucesso!',
                    text: 'Atendimento registrado com sucesso.',
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
                text: 'Ocorreu um erro ao registrar o atendimento.',
                customClass: {
                    confirmButton: 'btn btn-danger'
                }
            });
        });
    });
});
</script>