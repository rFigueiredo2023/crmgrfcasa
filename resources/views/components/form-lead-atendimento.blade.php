<!-- Modal Novo Lead com Atendimento -->
<div class="modal fade" id="modalNovoLeadAtendimento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-header border-bottom pb-3">
                <div>
                    <h3 class="modal-title mb-1">Novo Lead com Atendimento</h3>
                    <p class="text-muted mb-0">Preencha os dados do lead e registre o primeiro atendimento</p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNovoLeadAtendimento" action="{{ route('atendimentos.store-lead') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body py-4">
                    <div class="row g-4">
                        <!-- Dados do Lead -->
                        <div class="col-md-6">
                            <div class="card shadow-none border">
                                <div class="card-header border-bottom">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-user-circle fs-3 me-2 text-primary"></i>
                                        <h5 class="card-title mb-0">Dados do Lead</h5>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">Nome/Empresa</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bx bx-buildings"></i></span>
                                                <input type="text" class="form-control" name="nome_empresa" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">CNPJ</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bx bx-id-card"></i></span>
                                                <input type="text" class="form-control cnpj-mask" name="cnpj">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Inscrição Estadual</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bx bx-card"></i></span>
                                                <input type="text" class="form-control" name="ie">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Telefone</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bx bx-phone"></i></span>
                                                <input type="text" class="form-control phone-mask" name="telefone" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Contato</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bx bx-user"></i></span>
                                                <input type="text" class="form-control" name="contato" required>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label">Endereço</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bx bx-map"></i></span>
                                                <input type="text" class="form-control" name="endereco">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Código IBGE</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bx bx-hash"></i></span>
                                                <input type="text" class="form-control" name="codigo_ibge">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Dados do Atendimento -->
                        <div class="col-md-6">
                            <div class="card shadow-none border">
                                <div class="card-header border-bottom">
                                    <div class="d-flex align-items-center">
                                        <i class="bx bx-headphone fs-3 me-2 text-primary"></i>
                                        <h5 class="card-title mb-0">Dados do Atendimento</h5>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-12">
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
                                        <div class="col-12">
                                            <label class="form-label">Descrição</label>
                                            <textarea class="form-control" name="descricao" rows="3" required placeholder="Descreva o atendimento..."></textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Próxima Ação</label>
                                            <textarea class="form-control" name="proxima_acao" rows="2" placeholder="Defina a próxima ação a ser tomada..."></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Data da Próxima Ação</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                                <input type="date" class="form-control" name="data_proxima_acao">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Retorno</label>
                                            <textarea class="form-control" name="retorno" rows="2" placeholder="Registre o retorno do cliente..."></textarea>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Data de Retorno</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bx bx-calendar"></i></span>
                                                <input type="date" class="form-control" name="data_retorno">
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Anexo</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bx bx-paperclip"></i></span>
                                                <input type="file" class="form-control" name="anexo">
                                            </div>
                                            <small class="text-muted">Arquivos permitidos: PDF, JPG, JPEG, PNG, GIF (máx. 5MB)</small>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-check form-switch">
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
                    </div>
                </div>
                <div class="modal-footer border-top pt-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bx bx-x me-1"></i>
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bx bx-save me-1"></i>
                        Salvar
                    </button>
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

        // Adiciona classe de loading aos botões
        var submitBtn = form.find('button[type="submit"]');
        var originalText = submitBtn.html();
        submitBtn.html('<i class="bx bx-loader-alt bx-spin me-1"></i>Salvando...').prop('disabled', true);

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
            },
            complete: function() {
                // Restaura o botão
                submitBtn.html(originalText).prop('disabled', false);
            }
        });
    });

    // Animação suave ao abrir o modal
    $('#modalNovoLeadAtendimento').on('show.bs.modal', function() {
        $(this).find('.modal-content').addClass('animate__animated animate__fadeIn');
    });
});
</script>
@endpush

@push('styles')
<style>
.modal-content {
    border: none;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
}

.modal-header {
    background-color: #fff;
    border-radius: 0.5rem 0.5rem 0 0;
}

.modal-footer {
    background-color: #fff;
    border-radius: 0 0 0.5rem 0.5rem;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
}

.input-group-text {
    background-color: #f8f9fa;
    border-right: none;
}

.input-group .form-control {
    border-left: none;
}

.input-group .form-control:focus {
    border-color: #dee2e6;
    box-shadow: none;
}

.input-group:focus-within .input-group-text {
    border-color: #696cff;
    color: #696cff;
}

.input-group:focus-within .form-control {
    border-color: #696cff;
}

.form-check-input:checked {
    background-color: #696cff;
    border-color: #696cff;
}

textarea {
    resize: none;
}

.btn-primary {
    background-color: #696cff;
    border-color: #696cff;
}

.btn-primary:hover {
    background-color: #5f65e5;
    border-color: #5f65e5;
}

.text-primary {
    color: #696cff !important;
}

.animate__animated {
    animation-duration: 0.4s;
}
</style>
@endpush 