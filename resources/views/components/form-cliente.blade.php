{{-- Componente Form cliente --}}
<!-- Modal Cliente -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
          <input type="hidden" id="atividades_secundarias_json" name="atividades_secundarias">

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="razao_social">Razão Social *</label>
              <input type="text" class="form-control" id="razao_social" name="razao_social" required value="{{ old('razao_social') }}">
              <div class="invalid-feedback">Razão Social é obrigatória</div>
              @error('razao_social') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="email">Email</label>
              <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
              @error('email') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <x-campo-cnpj
                id="campo-cnpj-cliente"
                :campos="[
                  'razao_social' => 'company.name',
                  'email' => 'company.email',
                  'endereco' => 'address',
                  'municipio' => 'address.city',
                  'uf' => 'address.state',
                  'cep' => 'address.zip',
                  'telefone' => 'phones.0.number'
                ]"
                modalSelector="#clienteModal"
                value="{{ old('cnpj') }}"
              />
              @error('cnpj') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="inscricao_estadual">Inscrição Estadual</label>
              <input type="text" class="form-control" id="inscricao_estadual" name="inscricao_estadual" value="{{ old('inscricao_estadual') }}">
              @error('inscricao_estadual') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
          </div>

          <!-- Nova seção para atividades econômicas -->
          <div class="row mb-3">
            <div class="col-12">
              <label class="form-label" for="atividade_principal">Atividade Principal</label>
              <input type="text" class="form-control" id="atividade_principal" name="atividade_principal" value="{{ old('atividade_principal') }}" readonly>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-12">
              <label class="form-label">Atividades Secundárias</label>
              <div id="atividades_secundarias" class="border p-2 rounded bg-light" style="min-height: 40px; max-height: 120px; overflow-y: auto;">
                <p class="text-muted mb-0 small">As atividades secundárias serão exibidas aqui.</p>
              </div>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="telefone">Telefone *</label>
              <input type="text" class="form-control" id="telefone" name="telefone" required value="{{ old('telefone') }}">
              <div class="invalid-feedback">Telefone é obrigatório</div>
              @error('telefone') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="contato">Contato *</label>
              <input type="text" class="form-control" id="contato" name="contato" required value="{{ old('contato') }}">
              <div class="invalid-feedback">Contato é obrigatório</div>
              @error('contato') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="segmento">Segmento</label>
              <input type="text" class="form-control" id="segmento" name="segmento" value="{{ old('segmento') }}">
              @error('segmento') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label" for="inscricao_municipal">Inscrição Municipal</label>
              <input type="text" class="form-control" id="inscricao_municipal" name="inscricao_municipal" value="{{ old('inscricao_municipal') }}">
              @error('inscricao_municipal') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="porte_empresa">Porte da Empresa</label>
              <select class="form-select" id="porte_empresa" name="porte_empresa">
                <option value="">Selecione...</option>
                <option value="ME" {{ old('porte_empresa') == 'ME' ? 'selected' : '' }}>Microempresa (ME)</option>
                <option value="EPP" {{ old('porte_empresa') == 'EPP' ? 'selected' : '' }}>Empresa de Pequeno Porte (EPP)</option>
                <option value="MEI" {{ old('porte_empresa') == 'MEI' ? 'selected' : '' }}>Microempreendedor Individual (MEI)</option>
                <option value="MEDIO" {{ old('porte_empresa') == 'MEDIO' ? 'selected' : '' }}>Médio Porte</option>
                <option value="GRANDE" {{ old('porte_empresa') == 'GRANDE' ? 'selected' : '' }}>Grande Porte</option>
              </select>
              @error('porte_empresa') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-4">
              <label class="form-label" for="cep">CEP</label>
              <input type="text" class="form-control" id="cep" name="cep" value="{{ old('cep') }}">
              @error('cep') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-8">
              <label class="form-label" for="endereco">Endereço *</label>
              <input type="text" class="form-control" id="endereco" name="endereco" required value="{{ old('endereco') }}">
              <div class="invalid-feedback">Endereço é obrigatório</div>
              @error('endereco') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-4">
              <label class="form-label" for="codigo_ibge">Código IBGE *</label>
              <input type="text" class="form-control" id="codigo_ibge" name="codigo_ibge" required value="{{ old('codigo_ibge') }}">
              <div class="invalid-feedback">Código IBGE é obrigatório</div>
              @error('codigo_ibge') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-4">
              <label class="form-label" for="municipio">Município *</label>
              <input type="text" class="form-control" id="municipio" name="municipio" required value="{{ old('municipio') }}">
              <div class="invalid-feedback">Município é obrigatório</div>
              @error('municipio') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-4">
              <label class="form-label" for="uf">UF *</label>
              <input type="text" class="form-control" id="uf" name="uf" required maxlength="2" value="{{ old('uf') }}">
              <div class="invalid-feedback">UF é obrigatória</div>
              @error('uf') <small class="text-danger">{{ $message }}</small> @enderror
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

  // Código de máscara com verificação de segurança
  if (window.jQuery && jQuery().inputmask && $("#telefone").length) {
    try {
      $("#telefone").inputmask({
        mask: ['(99) 9999-9999', '(99) 99999-9999'],
        keepStatic: true
      });
    } catch (e) {
      console.warn("Erro ao aplicar máscara no telefone:", e);
    }
  }

  if (window.jQuery && jQuery().inputmask && $("#cep").length) {
    try {
      $("#cep").inputmask('99999-999');
    } catch (e) {
      console.warn("Erro ao aplicar máscara no CEP:", e);
    }

    // Buscar endereço ao preencher CEP
    try {
      $("#cep").blur(function() {
        const cep = $(this).val().replace(/\D/g, '');

        if (cep.length === 8) {
          $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, function(data) {
            if (!data.erro) {
              $("#endereco").val(data.logradouro);
              $("#municipio").val(data.localidade);
              $("#uf").val(data.uf);
              // Tenta buscar o código IBGE
              $("#codigo_ibge").val(data.ibge || '');
            }
          });
        }
      });
    } catch (e) {
      console.warn("Erro ao configurar busca de CEP:", e);
    }
  }

  // Ao abrir o modal para editar
  $('#clienteModal').on('show.bs.modal', function(event) {
    const button = $(event.relatedTarget);
    const clienteId = button.data('id');

    // Limpar formulário
    clienteForm.reset();

    // Limpar as atividades secundárias
    const atividadesSecundarias = document.getElementById('atividades_secundarias');
    atividadesSecundarias.innerHTML = '<p class="text-muted mb-0 small">As atividades secundárias serão exibidas aqui.</p>';

    if (clienteId) {
      // Modo edição
      modalTitle.textContent = 'Editar Cliente';

      // Buscar dados do cliente
      fetch(`/clientes/${clienteId}/edit`)
        .then(response => response.json())
        .then(data => {
          document.getElementById('cliente_id').value = data.id;
          document.getElementById('razao_social').value = data.razao_social;
          document.getElementById('email').value = data.email || '';
          document.getElementById('campo-cnpj-cliente').value = data.cnpj;
          document.getElementById('inscricao_estadual').value = data.inscricao_estadual || '';
          document.getElementById('telefone').value = data.telefone;
          document.getElementById('contato').value = data.contato;
          document.getElementById('segmento').value = data.segmento || '';
          document.getElementById('cep').value = data.cep || '';
          document.getElementById('endereco').value = data.endereco;
          document.getElementById('codigo_ibge').value = data.codigo_ibge;
          document.getElementById('municipio').value = data.municipio;
          document.getElementById('uf').value = data.uf;

          // Se tiver atividade principal, mostrar
          if (data.atividade_principal) {
            document.getElementById('atividade_principal').value = data.atividade_principal;
          }

          // Se tiver atividades secundárias, mostrar
          if (data.atividades_secundarias) {
            exibirAtividadesSecundarias(JSON.parse(data.atividades_secundarias));
          }
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
    const url = clienteId ? `/clientes/${clienteId}` : '/clientes';
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

// Função para exibir atividades secundárias na interface
function exibirAtividadesSecundarias(atividades) {
  const container = document.getElementById('atividades_secundarias');
  container.innerHTML = '';

  // Atualizar o campo oculto com o JSON das atividades
  document.getElementById('atividades_secundarias_json').value = JSON.stringify(atividades);

  if (!atividades || atividades.length === 0) {
    container.innerHTML = '<p class="text-muted mb-0 small">Nenhuma atividade secundária registrada.</p>';
    return;
  }

  const lista = document.createElement('ul');
  lista.className = 'list-unstyled mb-0 small';

  atividades.forEach(atividade => {
    const item = document.createElement('li');
    item.className = 'mb-1';
    item.textContent = atividade.text || atividade;
    lista.appendChild(item);
  });

  container.appendChild(lista);
}
</script>
