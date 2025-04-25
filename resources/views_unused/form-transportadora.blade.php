{{-- Componente Form transportadora --}}
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
              <label class="form-label" for="razao_social">Razão Social *</label>
              <input type="text" class="form-control" id="razao_social" name="razao_social" required>
              <div class="invalid-feedback">Razão Social é obrigatória</div>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="email">Email *</label>
              <input type="email" class="form-control" id="email" name="email" required>
              <div class="invalid-feedback">Email é obrigatório</div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="cnpj">CNPJ *</label>
              <input type="text" class="form-control" id="cnpj" name="cnpj" required>
              <div class="invalid-feedback">CNPJ é obrigatório</div>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="inscricao_estadual">Inscrição Estadual</label>
              <input type="text" class="form-control" id="inscricao_estadual" name="inscricao_estadual">
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="telefone">Telefone *</label>
              <input type="text" class="form-control" id="telefone" name="telefone" required>
              <div class="invalid-feedback">Telefone é obrigatório</div>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="celular">Celular</label>
              <input type="text" class="form-control" id="celular" name="celular">
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label class="form-label" for="endereco">Endereço *</label>
              <input type="text" class="form-control" id="endereco" name="endereco" required>
              <div class="invalid-feedback">Endereço é obrigatório</div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="codigo_ibge">Código IBGE *</label>
              <input type="text" class="form-control" id="codigo_ibge" name="codigo_ibge" required>
              <div class="invalid-feedback">Código IBGE é obrigatório</div>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="contato">Contato *</label>
              <input type="text" class="form-control" id="contato" name="contato" required>
              <div class="invalid-feedback">Contato é obrigatório</div>
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

  if ($("#celular").length) {
    $("#celular").inputmask({
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
          document.getElementById('razao_social').value = data.razao_social;
          document.getElementById('email').value = data.email || '';
          document.getElementById('cnpj').value = data.cnpj;
          document.getElementById('inscricao_estadual').value = data.inscricao_estadual || '';
          document.getElementById('telefone').value = data.telefone || '';
          document.getElementById('celular').value = data.celular || '';
          document.getElementById('endereco').value = data.endereco || '';
          document.getElementById('codigo_ibge').value = data.codigo_ibge || '';
          document.getElementById('contato').value = data.contato || '';
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
