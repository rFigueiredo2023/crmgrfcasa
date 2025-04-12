<!-- Modal de Novo Atendimento -->
<div class="modal fade" id="modalAtendimento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-dialog-centered">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Novo Atendimento</h3>
                    <p class="text-muted">Preencha os dados do atendimento</p>
                </div>

                <form action="{{ route('atendimentos.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="cliente_id" id="cliente_id">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Cliente</label>
                            <input type="text" class="form-control" id="cliente_nome" readonly>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tipo de Atendimento</label>
                            <select class="form-select" name="tipo_atendimento" required>
                                <option value="">Selecione o tipo</option>
                                <option value="Visita">Visita</option>
                                <option value="Telefone">Telefone</option>
                                <option value="Email">Email</option>
                                <option value="WhatsApp">WhatsApp</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="">Selecione o status</option>
                                <option value="Pendente">Pendente</option>
                                <option value="Em Andamento">Em Andamento</option>
                                <option value="Concluído">Concluído</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Tipo de Contato</label>
                            <select class="form-select" name="tipo" required>
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
                                <input class="form-check-input" type="checkbox" name="ativar_lembrete">
                                <label class="form-check-label">
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
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Salvar</button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Preenche os dados do cliente no modal
        const modal = document.getElementById('modalAtendimento');
        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const clienteId = button.getAttribute('data-cliente-id');
            const clienteNome = button.getAttribute('data-cliente-nome');

            document.getElementById('cliente_id').value = clienteId;
            document.getElementById('cliente_nome').value = clienteNome;
        });
    });
</script>
@endpush