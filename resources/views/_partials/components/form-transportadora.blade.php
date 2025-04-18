<!-- Modal Transportadora -->
<div class="modal fade" id="transportadoraModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="transportadoraModalTitle">Cadastrar Transportadora</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="transportadoraForm" method="POST">
          @csrf
          <input type="hidden" id="transportadora_id" name="id">

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="nome">Nome *</label>
              <input type="text" class="form-control" id="nome" name="nome" required>
              <div class="invalid-feedback">Nome é obrigatório</div>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="email">Email</label>
              <input type="email" class="form-control" id="email" name="email">
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="cnpj">CNPJ *</label>
              <input type="text" class="form-control" id="cnpj" name="cnpj" required>
              <div class="invalid-feedback">CNPJ é obrigatório</div>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="telefone">Telefone</label>
              <input type="text" class="form-control" id="telefone" name="telefone">
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label class="form-label" for="endereco">Endereço</label>
              <input type="text" class="form-control" id="endereco" name="endereco">
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label class="form-label" for="observacoes">Observações</label>
              <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
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

<!-- Script para Formulário de Transportadora -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const transportadoraForm = document.getElementById('transportadoraForm');
  const transportadoraModalTitle = document.getElementById('transportadoraModalTitle');

  // Aplicar máscaras aos campos
  if ($("#cnpj").length) {
    $("#cnpj").inputmask({
      mask: '99.999.999/9999-99',
      keepStatic: true
    });
  }

  if ($("#telefone").length) {
    $("#telefone").inputmask({
      mask: ['(99) 9999-9999', '(99) 99999-9999'],
      keepStatic: true
    });
  }

  // Ao abrir o modal para editar
  $('#transportadoraModal').on('show.bs.modal', function(event) {
    const button = $(event.relatedTarget);
    const transportadoraId = button.data('id');

    // Limpar formulário
    transportadoraForm.reset();

    if (transportadoraId) {
      // Modo edição
      transportadoraModalTitle.textContent = 'Editar Transportadora';

      // Buscar dados da transportadora
      fetch(`/api/transportadoras/${transportadoraId}`)
        .then(response => response.json())
        .then(data => {
          document.getElementById('transportadora_id').value = data.id;
          document.getElementById('nome').value = data.nome;
          document.getElementById('email').value = data.email || '';
          document.getElementById('cnpj').value = data.cnpj;
          document.getElementById('telefone').value = data.telefone || '';
          document.getElementById('endereco').value = data.endereco || '';
          document.getElementById('observacoes').value = data.observacoes || '';
        })
        .catch(error => {
          console.error('Erro ao carregar dados da transportadora:', error);
          Swal.fire({
            title: 'Erro!',
            text: 'Não foi possível carregar os dados da transportadora.',
            icon: 'error'
          });
        });
    } else {
      // Modo cadastro
      transportadoraModalTitle.textContent = 'Cadastrar Transportadora';
      document.getElementById('transportadora_id').value = '';
    }
  });

  // Envio do formulário
  transportadoraForm.addEventListener('submit', function(e) {
    e.preventDefault();

    // Validar campos obrigatórios
    const requiredFields = transportadoraForm.querySelectorAll('[required]');
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

    const transportadoraId = document.getElementById('transportadora_id').value;
    const formData = new FormData(transportadoraForm);
    const url = transportadoraId ? `/api/transportadoras/${transportadoraId}` : '/api/transportadoras';

    // Adicionar método PUT se for edição
    if (transportadoraId) {
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
          $('#transportadoraModal').modal('hide');

          // Recarregar a página para atualizar a tabela
          window.location.reload();
        });
      } else {
        throw new Error(data.message || 'Erro ao salvar transportadora');
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
