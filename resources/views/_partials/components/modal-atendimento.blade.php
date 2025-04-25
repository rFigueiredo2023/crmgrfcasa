{{-- Componente Modal atendimento --}}
<!-- Modal de Atendimento -->
<div class="modal fade" id="atendimentoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="atendimentoModalTitle">Registrar Atendimento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="atendimentoForm" method="POST">
          @csrf
          <input type="hidden" id="atendimento_id" name="id">
          <input type="hidden" id="cliente_id" name="cliente_id">
          <input type="hidden" id="lead_id" name="lead_id">
          <input type="hidden" id="entity_type" name="entity_type">

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="tipo_contato">Tipo de Contato *</label>
              <select class="form-select" id="tipo_contato" name="tipo_contato" required>
                <option value="">Selecione</option>
                <option value="Ligação">Ligação</option>
                <option value="WhatsApp">WhatsApp</option>
                <option value="E-mail">E-mail</option>
                <option value="Visita">Visita</option>
                <option value="Reunião">Reunião</option>
                <option value="Outro">Outro</option>
              </select>
              <div class="invalid-feedback">Tipo de contato é obrigatório</div>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="status">Status *</label>
              <select class="form-select" id="status" name="status" required>
                <option value="">Selecione</option>
                <option value="aberto">Aberto</option>
                <option value="aguardando">Aguardando Retorno</option>
                <option value="em_andamento">Em Andamento</option>
                <option value="concluido">Concluído</option>
                <option value="cancelado">Cancelado</option>
              </select>
              <div class="invalid-feedback">Status é obrigatório</div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label class="form-label" for="descricao">Descrição *</label>
              <textarea class="form-control" id="descricao" name="descricao" rows="4" required></textarea>
              <div class="invalid-feedback">Descrição é obrigatória</div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="proxima_acao">Próxima Ação</label>
              <textarea class="form-control" id="proxima_acao" name="proxima_acao" rows="2"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="data_proxima_acao">Data da Próxima Ação</label>
              <input type="date" class="form-control" id="data_proxima_acao" name="data_proxima_acao">
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="retorno">Retorno</label>
              <textarea class="form-control" id="retorno" name="retorno" rows="2"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="data_retorno">Data de Retorno</label>
              <input type="date" class="form-control" id="data_retorno" name="data_retorno">
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="ativar_lembrete" name="ativar_lembrete" value="1">
                <label class="form-check-label" for="ativar_lembrete">Ativar lembrete</label>
              </div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label class="form-label" for="anexo">Anexo</label>
              <input type="file" class="form-control" id="anexo" name="anexo">
              <div class="form-text">Arquivos permitidos: pdf, jpg, jpeg, png (máx: 5MB)</div>
            </div>
          </div>

          <div class="row">
            <div class="col-12 text-center">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!-- Script para Formulário de Atendimento -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const atendimentoForm = document.getElementById('atendimentoForm');
  const atendimentoModalTitle = document.getElementById('atendimentoModalTitle');

  // Ao abrir o modal
  $('#atendimentoModal').on('show.bs.modal', function(event) {
    const button = $(event.relatedTarget);
    const atendimentoId = button.data('id');
    const clienteId = button.data('cliente-id');
    const leadId = button.data('lead-id');
    const entityType = leadId ? 'lead' : 'cliente';

    // Limpar formulário
    atendimentoForm.reset();

    // Definir o tipo de entidade e ID
    document.getElementById('entity_type').value = entityType;

    if (clienteId) {
      document.getElementById('cliente_id').value = clienteId;
      document.getElementById('lead_id').value = '';
    } else if (leadId) {
      document.getElementById('lead_id').value = leadId;
      document.getElementById('cliente_id').value = '';
    }

    if (atendimentoId) {
      // Modo edição
      atendimentoModalTitle.textContent = 'Editar Atendimento';

      // Buscar dados do atendimento
      fetch(`/api/atendimentos/${atendimentoId}`)
        .then(response => response.json())
        .then(data => {
          document.getElementById('atendimento_id').value = data.id;
          document.getElementById('tipo_contato').value = data.tipo_contato;
          document.getElementById('descricao').value = data.descricao;
          document.getElementById('status').value = data.status;
          document.getElementById('retorno').value = data.retorno || '';
          document.getElementById('data_retorno').value = data.data_retorno || '';
          document.getElementById('proxima_acao').value = data.proxima_acao || '';
          document.getElementById('data_proxima_acao').value = data.data_proxima_acao || '';
          document.getElementById('ativar_lembrete').checked = data.ativar_lembrete === 1;
        })
        .catch(error => {
          console.error('Erro ao carregar dados do atendimento:', error);
          Swal.fire({
            title: 'Erro!',
            text: 'Não foi possível carregar os dados do atendimento.',
            icon: 'error'
          });
        });
    } else {
      // Modo cadastro
      atendimentoModalTitle.textContent = 'Registrar Atendimento';
      document.getElementById('atendimento_id').value = '';
    }
  });

  // Envio do formulário
  atendimentoForm.addEventListener('submit', function(e) {
    e.preventDefault();

    // Validar campos obrigatórios
    const requiredFields = atendimentoForm.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
      if (!field.value.trim()) {
        field.classList.add('is-invalid');
        isValid = false;
      } else {
        field.classList.remove('is-invalid');
      }
    });

    if (!isValid) return;

    const atendimentoId = document.getElementById('atendimento_id').value;
    const formData = new FormData(atendimentoForm);

    // Definir a URL com base no tipo de entidade (lead ou cliente)
    const entityType = document.getElementById('entity_type').value;
    const clienteId = document.getElementById('cliente_id').value;
    const leadId = document.getElementById('lead_id').value;

    let url;
    if (atendimentoId) {
      url = `/api/atendimentos/${atendimentoId}`;
      formData.append('_method', 'PUT');
    } else {
      if (entityType === 'lead') {
        url = `/api/leads/${leadId}/atendimentos`;
      } else {
        url = `/api/clientes/${clienteId}/atendimentos`;
      }
    }

    fetch(url, {
      method: 'POST',
      body: formData,
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        Swal.fire({
          title: 'Sucesso!',
          text: data.message,
          icon: 'success'
        }).then(() => {
          // Fechar o modal
          $('#atendimentoModal').modal('hide');

          // Recarregar a página para atualizar a tabela
          window.location.reload();
        });
      } else {
        throw new Error(data.message || 'Erro ao salvar atendimento');
      }
    })
    .catch(error => {
      console.error('Erro:', error);
      Swal.fire({
        title: 'Erro!',
        text: error.message || 'Ocorreu um erro ao processar sua solicitação.',
        icon: 'error'
      });
    });
  });
});
</script>
