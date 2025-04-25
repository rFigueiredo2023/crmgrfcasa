{{-- Componente Form cliente --}}
<!-- Modal Cliente -->
<div class="modal fade" id="clienteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalTitle">Cadastrar Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="clienteForm" method="POST">
          @csrf
          <input type="hidden" id="cliente_id" name="id">

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="nome">Nome *</label>
              <input type="text" class="form-control" id="nome" name="nome" required>
              <div class="invalid-feedback">Nome é obrigatório</div>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="email">Email *</label>
              <input type="email" class="form-control" id="email" name="email" required>
              <div class="invalid-feedback">Email válido é obrigatório</div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="cpf_cnpj">CPF/CNPJ *</label>
              <input type="text" class="form-control" id="cpf_cnpj" name="cpf_cnpj" required>
              <div class="invalid-feedback">CPF/CNPJ é obrigatório</div>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="telefone">Telefone *</label>
              <input type="text" class="form-control" id="telefone" name="telefone" required>
              <div class="invalid-feedback">Telefone é obrigatório</div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-4">
              <label class="form-label" for="cep">CEP</label>
              <input type="text" class="form-control" id="cep" name="cep">
            </div>
            <div class="col-md-8">
              <label class="form-label" for="endereco">Endereço</label>
              <input type="text" class="form-control" id="endereco" name="endereco">
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-4">
              <label class="form-label" for="bairro">Bairro</label>
              <input type="text" class="form-control" id="bairro" name="bairro">
            </div>
            <div class="col-md-4">
              <label class="form-label" for="cidade">Cidade</label>
              <input type="text" class="form-control" id="cidade" name="cidade">
            </div>
            <div class="col-md-4">
              <label class="form-label" for="estado">Estado</label>
              <input type="text" class="form-control" id="estado" name="estado">
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

<!-- Script para Formulário de Cliente -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const clienteForm = document.getElementById('clienteForm');
  const modalTitle = document.getElementById('modalTitle');

  // Aplicar máscaras aos campos
  if ($("#cpf_cnpj").length) {
    $("#cpf_cnpj").inputmask({
      mask: ['999.999.999-99', '99.999.999/9999-99'],
      keepStatic: true
    });
  }

  if ($("#telefone").length) {
    $("#telefone").inputmask({
      mask: ['(99) 9999-9999', '(99) 99999-9999'],
      keepStatic: true
    });
  }

  if ($("#cep").length) {
    $("#cep").inputmask('99999-999');

    // Buscar endereço ao preencher CEP
    $("#cep").blur(function() {
      const cep = $(this).val().replace(/\D/g, '');

      if (cep.length === 8) {
        $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, function(data) {
          if (!data.erro) {
            $("#endereco").val(data.logradouro);
            $("#bairro").val(data.bairro);
            $("#cidade").val(data.localidade);
            $("#estado").val(data.uf);
          }
        });
      }
    });
  }

  // Ao abrir o modal para editar
  $('#clienteModal').on('show.bs.modal', function(event) {
    const button = $(event.relatedTarget);
    const clienteId = button.data('id');

    // Limpar formulário
    clienteForm.reset();

    if (clienteId) {
      // Modo edição
      modalTitle.textContent = 'Editar Cliente';

      // Buscar dados do cliente
      fetch(`/api/clientes/${clienteId}`)
        .then(response => response.json())
        .then(data => {
          document.getElementById('cliente_id').value = data.id;
          document.getElementById('nome').value = data.nome;
          document.getElementById('email').value = data.email;
          document.getElementById('cpf_cnpj').value = data.cpf_cnpj;
          document.getElementById('telefone').value = data.telefone;
          document.getElementById('cep').value = data.cep;
          document.getElementById('endereco').value = data.endereco;
          document.getElementById('bairro').value = data.bairro;
          document.getElementById('cidade').value = data.cidade;
          document.getElementById('estado').value = data.estado;
          document.getElementById('observacoes').value = data.observacoes;
        })
        .catch(error => {
          console.error('Erro ao carregar dados do cliente:', error);
          Swal.fire({
            title: 'Erro!',
            text: 'Não foi possível carregar os dados do cliente.',
            icon: 'error'
          });
        });
    } else {
      // Modo cadastro
      modalTitle.textContent = 'Cadastrar Cliente';
      document.getElementById('cliente_id').value = '';
    }
  });

  // Envio do formulário
  clienteForm.addEventListener('submit', function(e) {
    e.preventDefault();

    // Validar campos obrigatórios
    const requiredFields = clienteForm.querySelectorAll('[required]');
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

    const clienteId = document.getElementById('cliente_id').value;
    const formData = new FormData(clienteForm);
    const url = clienteId ? `/api/clientes/${clienteId}` : '/api/clientes';
    const method = clienteId ? 'PUT' : 'POST';

    // Adicionar método PUT se for edição
    if (clienteId) {
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
          $('#clienteModal').modal('hide');

          // Recarregar a página para atualizar a tabela
          window.location.reload();
        });
      } else {
        throw new Error(data.message || 'Erro ao salvar cliente');
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
