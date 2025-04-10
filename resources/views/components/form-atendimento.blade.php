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
                        <div class="col-md-6">
                            <label class="form-label">Data do Atendimento</label>
                            <input type="datetime-local" class="form-control" name="data_atendimento" required>
                        </div>
                        <div class="col-md-6">
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
                            <label class="form-label">Descrição</label>
                            <textarea class="form-control" name="descricao" rows="4" required></textarea>
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