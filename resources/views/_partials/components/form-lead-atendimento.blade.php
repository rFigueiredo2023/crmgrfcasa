{{-- Componente Form lead atendimento --}}
<!-- Modal Lead com Atendimento -->
<div class="modal fade" id="leadModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="leadModalTitle">Cadastrar Lead</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="leadForm" method="POST">
          @csrf
          <input type="hidden" id="lead_id" name="id">

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="razao_social">Nome/Razão Social *</label>
              <input type="text" class="form-control" id="razao_social" name="razao_social" required>
              <div class="invalid-feedback">Nome/Razão Social é obrigatório</div>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="email">Email *</label>
              <input type="email" class="form-control" id="email" name="email" required>
              <div class="invalid-feedback">Email válido é obrigatório</div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="telefone">Telefone *</label>
              <input type="text" class="form-control" id="telefone" name="telefone" required>
              <div class="invalid-feedback">Telefone é obrigatório</div>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="cnpj">CNPJ</label>
              <input type="text" class="form-control" id="cnpj" name="cnpj">
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label class="form-label" for="endereco">Endereço</label>
              <input type="text" class="form-control" id="endereco" name="endereco">
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="contato">Nome do Contato</label>
              <input type="text" class="form-control" id="contato" name="contato">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="ie">Inscrição Estadual</label>
              <input type="text" class="form-control" id="ie" name="inscricao_estadual">
            </div>
          </div>

          <!-- Seção de Atendimento (visível apenas na criação) -->
          <div id="atendimentoSection" class="mt-4">
            <h5 class="border-bottom pb-2">Registrar Atendimento Inicial</h5>

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
                </select>
                <div class="invalid-feedback">Status é obrigatório</div>
              </div>
            </div>

            <div class="row mb-3">
              <div class="col-md-12">
                <label class="form-label" for="descricao">Descrição do Atendimento *</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="3" required></textarea>
                <div class="invalid-feedback">Descrição do atendimento é obrigatória</div>
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

<!-- Script para Formulário de Lead com Atendimento -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const leadForm = document.getElementById('leadForm');
  const leadModalTitle = document.getElementById('leadModalTitle');
  const atendimentoSection = document.getElementById('atendimentoSection');

  // Aplicar máscaras aos campos
  if ($("#telefone").length) {
    $("#telefone").inputmask({
      mask: ['(99) 9999-9999', '(99) 99999-9999'],
      keepStatic: true
    });
  }

  if ($("#cnpj").length) {
    $("#cnpj").inputmask({
      mask: ['99.999.999/9999-99'],
      keepStatic: true
    });
  }

  // Ao abrir o modal para editar
  $('#leadModal').on('show.bs.modal', function(event) {
    const button = $(event.relatedTarget);
    const leadId = button.data('id');

    // Limpar formulário
    leadForm.reset();

    if (leadId) {
      // Modo edição
      leadModalTitle.textContent = 'Editar Lead';
      atendimentoSection.style.display = 'none';

      // Desativar validação para campos de atendimento em modo edição
      document.getElementById('tipo_contato').removeAttribute('required');
      document.getElementById('status').removeAttribute('required');
      document.getElementById('descricao').removeAttribute('required');

      // Buscar dados do lead
      fetch(`/api/leads/${leadId}`)
        .then(response => response.json())
        .then(data => {
          document.getElementById('lead_id').value = data.id;
          document.getElementById('razao_social').value = data.razao_social;
          document.getElementById('email').value = data.email;
          document.getElementById('telefone').value = data.telefone;
          document.getElementById('cnpj').value = data.cnpj || '';
          document.getElementById('endereco').value = data.endereco || '';
          document.getElementById('contato').value = data.contato || '';
          document.getElementById('ie').value = data.ie || '';
        })
        .catch(error => {
          console.error('Erro ao carregar dados do lead:', error);
          Swal.fire({
            title: 'Erro!',
            text: 'Não foi possível carregar os dados do lead.',
            icon: 'error'
          });
        });
    } else {
      // Modo cadastro
      leadModalTitle.textContent = 'Cadastrar Lead';
      atendimentoSection.style.display = 'block';
      document.getElementById('lead_id').value = '';

      // Ativar validação para campos de atendimento em modo criação
      document.getElementById('tipo_contato').setAttribute('required', '');
      document.getElementById('status').setAttribute('required', '');
      document.getElementById('descricao').setAttribute('required', '');
    }
  });

  // Envio do formulário
  leadForm.addEventListener('submit', function(e) {
    e.preventDefault();

    // Validar campos obrigatórios
    const requiredFields = leadForm.querySelectorAll('[required]');
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

    const leadId = document.getElementById('lead_id').value;
    const formData = new FormData(leadForm);

    // Definir a URL com base no modo (edição ou criação)
    let url = leadId ? `/api/leads/${leadId}` : '/api/leads/com-atendimento';
    let method = leadId ? 'PUT' : 'POST';

    // Adicionar método PUT se for edição
    if (leadId) {
      formData.append('_method', 'PUT');
    }

    fetch(url, {
      method: 'POST', // Sempre POST por causa do FormData
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
          $('#leadModal').modal('hide');

          // Recarregar a página para atualizar a tabela
          window.location.reload();
        });
      } else {
        throw new Error(data.message || 'Erro ao salvar lead');
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
