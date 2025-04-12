<!-- Modal Novo Lead com Atendimento -->
<div class="modal fade" id="modalNovoLeadAtendimento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Novo Lead com Atendimento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNovoLeadAtendimento" action="{{ route('atendimentos.store-lead') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Dados do Lead</h6>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Nome/Empresa</label>
                                    <input type="text" class="form-control" name="nome_empresa" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CNPJ</label>
                                    <input type="text" class="form-control cnpj-mask" name="cnpj">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Inscrição Estadual</label>
                                    <input type="text" class="form-control" name="ie">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telefone</label>
                                    <input type="text" class="form-control phone-mask" name="telefone" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Contato</label>
                                    <input type="text" class="form-control" name="contato" required>
                                </div>
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Endereço</label>
                                    <input type="text" class="form-control" name="endereco">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Código IBGE</label>
                                    <input type="text" class="form-control" name="codigo_ibge">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Dados do Atendimento</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tipo de Atendimento</label>
                                    <select class="form-select" name="tipo" required>
                                        <option value="">Selecione...</option>
                                        <option value="Ligação">Ligação</option>
                                        <option value="WhatsApp">WhatsApp</option>
                                        <option value="E-mail">E-mail</option>
                                        <option value="Visita">Visita</option>
                                        <option value="Reunião">Reunião</option>
                                        <option value="Outro">Outro</option>
                                    </select>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Descrição</label>
                                    <textarea class="form-control" name="descricao" rows="3" required></textarea>
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Próxima Ação</label>
                                    <textarea class="form-control" name="proxima_acao" rows="2"></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Data da Próxima Ação</label>
                                    <input type="date" class="form-control" name="data_proxima_acao">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Retorno</label>
                                    <textarea class="form-control" name="retorno" rows="2"></textarea>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Data de Retorno</label>
                                    <input type="date" class="form-control" name="data_retorno">
                                </div>
                                <div class="col-12 mb-3">
                                    <label class="form-label">Anexo</label>
                                    <input type="file" class="form-control" name="anexo">
                                    <small class="text-muted">Arquivos permitidos: PDF, JPG, JPEG, PNG, GIF (máx. 5MB)</small>
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="ativar_lembrete" id="ativar_lembrete_lead">
                                        <label class="form-check-label" for="ativar_lembrete_lead">
                                            Ativar lembrete
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
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

@push('scripts')
<script>
$(document).ready(function() {
    // Inicializa as máscaras
    $('.cnpj-mask').mask('00.000.000/0000-00');
    $('.phone-mask').mask('(00) 00000-0000');

    // Submit do formulário via AJAX
    $('#formNovoLeadAtendimento').on('submit', function(e) {
        e.preventDefault();
        
        var form = $(this);
        var formData = new FormData(this);

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Fecha o modal
                    $('#modalNovoLeadAtendimento').modal('hide');
                    
                    // Limpa o formulário
                    form[0].reset();
                    
                    // Mostra mensagem de sucesso
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 1500
                    });

                    // Recarrega a página após 1.5 segundos
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                }
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                var errorMessage = '';
                
                // Formata as mensagens de erro
                $.each(errors, function(key, value) {
                    errorMessage += value[0] + '<br>';
                });

                // Mostra mensagem de erro
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    html: errorMessage,
                    confirmButtonText: 'Ok'
                });
            }
        });
    });
});
</script>
@endpush 