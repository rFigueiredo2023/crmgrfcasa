<!-- Modal Veículo -->
<div class="modal fade" id="veiculoModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="veiculoModalTitle">Cadastrar Veículo</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="veiculoForm" method="POST">
          @csrf
          <input type="hidden" id="veiculo_id" name="id">
          <input type="hidden" id="cliente_id_veiculo" name="cliente_id">

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="placa">Placa *</label>
              <input type="text" class="form-control" id="placa" name="placa" required>
              <div class="invalid-feedback">Placa é obrigatória</div>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="modelo">Modelo *</label>
              <input type="text" class="form-control" id="modelo" name="modelo" required>
              <div class="invalid-feedback">Modelo é obrigatório</div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="marca">Marca *</label>
              <input type="text" class="form-control" id="marca" name="marca" required>
              <div class="invalid-feedback">Marca é obrigatória</div>
            </div>
            <div class="col-md-6">
              <label class="form-label" for="ano">Ano</label>
              <input type="number" class="form-control" id="ano" name="ano" min="1900" max="2099">
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="cor">Cor</label>
              <input type="text" class="form-control" id="cor" name="cor">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="tipo">Tipo</label>
              <select class="form-select" id="tipo" name="tipo">
                <option value="">Selecione</option>
                <option value="carro">Carro</option>
                <option value="caminhao">Caminhão</option>
                <option value="moto">Moto</option>
                <option value="outro">Outro</option>
              </select>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-12">
              <label class="form-label" for="observacoes_veiculo">Observações</label>
              <textarea class="form-control" id="observacoes_veiculo" name="observacoes" rows="3"></textarea>
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

<!-- Script para Formulário de Veículo -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  const veiculoForm = document.getElementById('veiculoForm');
  const veiculoModalTitle = document.getElementById('veiculoModalTitle');

  // Aplicar máscara à placa
  if ($("#placa").length) {
    $("#placa").inputmask({
      mask: 'AAA-9999',
      keepStatic: true
    });
  }

  // Ao abrir o modal para adicionar/editar
  $('#veiculoModal').on('show.bs.modal', function(event) {
    const button = $(event.relatedTarget);
    const veiculoId = button.data('id');
    const clienteId = button.data('cliente-id');

    // Limpar formulário
    veiculoForm.reset();

    // Sempre definir o cliente_id, mesmo em modo de edição
    if (clienteId) {
      document.getElementById('cliente_id_veiculo').value = clienteId;
    }

    if (veiculoId) {
      // Modo edição
      veiculoModalTitle.textContent = 'Editar Veículo';

      // Buscar dados do veículo
      fetch(`/api/veiculos/${veiculoId}`)
        .then(response => response.json())
        .then(data => {
          document.getElementById('veiculo_id').value = data.id;
          document.getElementById('placa').value = data.placa;
          document.getElementById('modelo').value = data.modelo;
          document.getElementById('marca').value = data.marca;
          document.getElementById('ano').value = data.ano || '';
          document.getElementById('cor').value = data.cor || '';
          document.getElementById('tipo').value = data.tipo || '';
          document.getElementById('observacoes_veiculo').value = data.observacoes || '';
        })
        .catch(error => {
          console.error('Erro ao carregar dados do veículo:', error);
          Swal.fire({
            title: 'Erro!',
            text: 'Não foi possível carregar os dados do veículo.',
            icon: 'error'
          });
        });
    } else {
      // Modo cadastro
      veiculoModalTitle.textContent = 'Cadastrar Veículo';
      document.getElementById('veiculo_id').value = '';
    }
  });

  // Envio do formulário
  veiculoForm.addEventListener('submit', function(e) {
    e.preventDefault();

    // Validar campos obrigatórios
    const requiredFields = veiculoForm.querySelectorAll('[required]');
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

    const veiculoId = document.getElementById('veiculo_id').value;
    const formData = new FormData(veiculoForm);
    const url = veiculoId ? `/api/veiculos/${veiculoId}` : '/api/veiculos';

    // Adicionar método PUT se for edição
    if (veiculoId) {
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
          $('#veiculoModal').modal('hide');

          // Recarregar a página ou apenas a tabela de veículos
          if (typeof loadVeiculos === 'function' && document.getElementById('cliente_id_veiculo').value) {
            loadVeiculos(document.getElementById('cliente_id_veiculo').value);
          } else {
            window.location.reload();
          }
        });
      } else {
        throw new Error(data.message || 'Erro ao salvar veículo');
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
