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
              <label class="form-label" for="tipo">Tipo de Atendimento *</label>
              <select class="form-select" id="tipo" name="tipo" required>
                <option value="">Selecione</option>
                <option value="primeiro_contato">Primeiro Contato</option>
                <option value="retorno">Retorno</option>
                <option value="proposta">Proposta</option>
                <option value="suporte">Suporte</option>
                <option value="duvida">Dúvida</option>
                <option value="reclamacao">Reclamação</option>
                <option value="outro">Outro</option>
              </select>
              <div class="invalid-feedback">Tipo de atendimento é obrigatório</div>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="canal">Canal *</label>
              <select class="form-select" id="canal" name="canal" required>
                <option value="">Selecione</option>
                <option value="telefone">Telefone</option>
                <option value="email">E-mail</option>
                <option value="whatsapp">WhatsApp</option>
                <option value="presencial">Presencial</option>
                <option value="video">Videoconferência</option>
                <option value="outro">Outro</option>
              </select>
              <div class="invalid-feedback">Canal é obrigatório</div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label class="form-label" for="titulo">Título *</label>
              <input type="text" class="form-control" id="titulo" name="titulo" required>
              <div class="invalid-feedback">Título é obrigatório</div>
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
            <div class="col-md-6">
              <label class="form-label" for="data_retorno">Data de Retorno</label>
              <input type="date" class="form-control" id="data_retorno" name="data_retorno">
            </div>
          </div>

          <div class="row mb-3" id="responsavelGroup">
            <div class="col-md-6">
              <label class="form-label" for="responsavel_id">Responsável</label>
              <select class="form-select" id="responsavel_id" name="responsavel_id">
                <option value="">Selecione</option>
                <!-- Opções de responsáveis serão carregadas via JavaScript -->
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="prioridade">Prioridade</label>
              <select class="form-select" id="prioridade" name="prioridade">
                <option value="">Selecione</option>
                <option value="baixa">Baixa</option>
                <option value="media">Média</option>
                <option value="alta">Alta</option>
                <option value="urgente">Urgente</option>
              </select>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label class="form-label" for="observacoes">Observações</label>
              <textarea class="form-control" id="observacoes" name="observacoes" rows="2"></textarea>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="notificar" name="notificar" value="1">
                <label class="form-check-label" for="notificar">Enviar notificação ao cliente</label>
              </div>
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
  const responsavelGroup = document.getElementById('responsavelGroup');

  // Carregar responsáveis
  carregarResponsaveis();

  function carregarResponsaveis() {
    // Adicionamos tratamento para falha silenciosa na API
    fetch('/api/usuarios')
      .then(response => {
        // Verificar se a resposta foi bem-sucedida (status 200-299)
        if (!response.ok) {
          // Em vez de lançar erro, apenas retornar array vazio e tratar silenciosamente
          return [];
        }
        return response.json();
      })
      .then(data => {
        // Verificar se data é array (pode não ser se a API retornar HTML em vez de JSON)
        if (!Array.isArray(data)) {
          // Silenciosamente usar array vazio
          data = [];
        }

        const responsavelSelect = document.getElementById('responsavel_id');
        // Limpar opções existentes
        responsavelSelect.innerHTML = '<option value="">Selecione</option>';

        // Adicionar opções de usuários
        data.forEach(usuario => {
          const option = document.createElement('option');
          option.value = usuario.id;
          option.textContent = usuario.name;
          responsavelSelect.appendChild(option);
        });
      })
      .catch(error => {
        // Apenas log simples, sem mostrar o erro completo
        if (localStorage.getItem('debug') === 'true') {
          console.error('Erro ao carregar responsáveis');
        }

        // Certificar que o select existe e tem pelo menos a opção default
        const responsavelSelect = document.getElementById('responsavel_id');
        if (responsavelSelect) {
          responsavelSelect.innerHTML = '<option value="">Selecione</option>';
        }
      });
  }

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
      fetch(`/atendimentos/${atendimentoId}/edit`)
        .then(response => response.json())
        .then(data => {
          document.getElementById('atendimento_id').value = data.id;
          document.getElementById('tipo').value = data.tipo;
          document.getElementById('canal').value = data.canal;
          document.getElementById('titulo').value = data.titulo;
          document.getElementById('descricao').value = data.descricao;
          document.getElementById('status').value = data.status;
          document.getElementById('data_retorno').value = data.data_retorno;
          document.getElementById('responsavel_id').value = data.responsavel_id || '';
          document.getElementById('prioridade').value = data.prioridade || '';
          document.getElementById('observacoes').value = data.observacoes || '';
          document.getElementById('notificar').checked = data.notificar === 1;
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
      // Definir usuário atual como responsável se disponível
      const usuarioAtualId = document.querySelector('meta[name="user-id"]')?.content;
      if (usuarioAtualId) {
        document.getElementById('responsavel_id').value = usuarioAtualId;
      }
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
    const url = atendimentoId ? `/atendimentos/${atendimentoId}` : '/atendimentos';

    // Adicionar método PUT se for edição
    if (atendimentoId) {
      formData.append('_method', 'PUT');
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
