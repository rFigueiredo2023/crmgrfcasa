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
              <label class="form-label" for="cnpj">CNPJ *</label>
              <input type="text" class="form-control" id="cnpj" name="cnpj" required value="{{ old('cnpj') }}" onblur="buscarCNPJ(this.value)">
              <div class="invalid-feedback">CNPJ é obrigatório</div>
              @error('cnpj') <small class="text-danger">{{ $message }}</small> @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label" for="inscricao_estadual">Inscrição Estadual</label>
              <input type="text" class="form-control" id="inscricao_estadual" name="inscricao_estadual" value="{{ old('inscricao_estadual') }}">
              @error('inscricao_estadual') <small class="text-danger">{{ $message }}</small> @enderror
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
  if (window.jQuery && jQuery().inputmask && $("#cnpj").length) {
    try {
      $("#cnpj").inputmask({
        mask: '99.999.999/9999-99',
        keepStatic: true
      });
    } catch (e) {
      console.warn("Erro ao aplicar máscara no CNPJ:", e);
    }
  }

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
          document.getElementById('cnpj').value = data.cnpj;
          document.getElementById('inscricao_estadual').value = data.inscricao_estadual || '';
          document.getElementById('telefone').value = data.telefone;
          document.getElementById('contato').value = data.contato;
          document.getElementById('segmento').value = data.segmento || '';
          document.getElementById('cep').value = data.cep || '';
          document.getElementById('endereco').value = data.endereco;
          document.getElementById('codigo_ibge').value = data.codigo_ibge;
          document.getElementById('municipio').value = data.municipio;
          document.getElementById('uf').value = data.uf;
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

  // Adicionar evento de busca de CNPJ de forma segura
  try {
    const camposCNPJ = document.querySelectorAll('input[name="cnpj"]');
    camposCNPJ.forEach(function(input) {
      input.addEventListener('blur', function() {
        buscarCNPJSimples(this.value);
      });
    });

    // Também adicionar via jQuery para compatibilidade com modais carregados depois
    if (window.jQuery) {
      $(document).on('blur', 'input[name="cnpj"]', function() {
        buscarCNPJSimples(this.value);
      });
    }
  } catch (e) {
    console.error("Erro ao configurar eventos de CNPJ:", e);
  }
});

// Função simplificada para buscar CNPJ sem depender de bibliotecas externas
function buscarCNPJSimples(cnpj) {
  console.log('Função buscarCNPJSimples chamada com:', cnpj);
  // Remove caracteres não numéricos
  cnpj = cnpj.replace(/\D/g, '');
  console.log('CNPJ sanitizado:', cnpj);

  if (cnpj.length !== 14) {
    console.log('CNPJ inválido - não tem 14 dígitos');
    // alert('CNPJ inválido.');
    return;
  }

  // Feedback via console em vez de alert
  console.log('Consultando CNPJ...');

  // URL do proxy Laravel
  const apiUrl = `/customers/api/consultar-cnpj/${cnpj}`;
  console.log('Consultando URL do proxy:', apiUrl);

  // Requisição com fetch
  fetch(apiUrl)
    .then(response => {
      console.log('Status da resposta:', response.status);
      // Verificar se a resposta tem conteúdo antes de tentar parsear JSON
      if (response.status !== 200) {
        throw new Error(`Erro na requisição: ${response.status}`);
      }

      return response.text().then(text => {
        // Verificar se o texto é um JSON válido antes de parsear
        if (!text || text.trim() === '') {
          throw new Error('Resposta vazia recebida da API');
        }

        try {
          return JSON.parse(text);
        } catch (e) {
          console.error('Erro ao parsear JSON:', text);
          throw new Error('Resposta inválida recebida da API');
        }
      });
    })
    .then(data => {
      console.log('Dados recebidos da API:', data);

      if (data.status === 'ERROR') {
        console.log('API retornou erro:', data.message);
        // alert('Não foi possível consultar o CNPJ.');
        return;
      }

      // Preenchimento dos campos
      document.querySelectorAll('input[name="razao_social"]').forEach(el => el.value = data.nome || '');
      document.querySelectorAll('input[name="inscricao_estadual"]').forEach(el => el.value = data.inscricao_estadual || '');
      document.querySelectorAll('input[name="endereco"]').forEach(el => el.value = `${data.logradouro || ''}, ${data.numero || ''}`);
      document.querySelectorAll('input[name="municipio"]').forEach(el => el.value = data.municipio || '');
      document.querySelectorAll('input[name="uf"]').forEach(el => el.value = data.uf || '');
      document.querySelectorAll('input[name="cep"]').forEach(el => el.value = data.cep || '');

      // Se o IBGE já veio na resposta da ReceitaWS, usar ele
      if (data.ibge) {
        document.querySelectorAll('input[name="codigo_ibge"]').forEach(el => el.value = data.ibge || '');
      }
      // Se não veio o IBGE, mas veio o município e UF, consultar a BrasilAPI
      else if (data.municipio && data.uf) {
        // Formatar município e UF para o formato esperado pela BrasilAPI
        const municipio = (data.municipio || '').toUpperCase().replace(/\s+/g, '-');
        const uf = (data.uf || '').toUpperCase();
        const cidadeFormatada = `${municipio}-${uf}`;

        console.log('Consultando BrasilAPI para código IBGE:', cidadeFormatada);

        // Buscar IBGE com a BrasilAPI
        fetch(`https://brasilapi.com.br/api/ibge/municipios/v1/${uf}`)
          .then(res => res.json())
          .then(municipios => {
            // BrasilAPI retorna uma lista de municípios do estado
            // Precisamos encontrar o que corresponde ao nome do município
            const municipioNormalizado = data.municipio.toUpperCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

            // Procurar o município na lista retornada
            const municipioEncontrado = municipios.find(m => {
              const nomeNormalizado = m.nome.toUpperCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
              return nomeNormalizado === municipioNormalizado ||
                     nomeNormalizado.includes(municipioNormalizado) ||
                     municipioNormalizado.includes(nomeNormalizado);
            });

            if (municipioEncontrado && municipioEncontrado.codigo_ibge) {
              console.log('Código IBGE encontrado:', municipioEncontrado.codigo_ibge);
              document.querySelectorAll('input[name="codigo_ibge"]').forEach(el => el.value = municipioEncontrado.codigo_ibge);
            } else {
              console.warn('Não foi possível encontrar o código IBGE para', data.municipio, data.uf);
            }
          })
          .catch(err => {
            console.warn('Não foi possível buscar o código IBGE na BrasilAPI:', err);
          });
      }

      // Feedback de conclusão via console
      console.log('Consulta concluída com sucesso!');
    })
    .catch(error => {
      console.error('Erro ao consultar CNPJ:', error);
      // alert('Erro ao consultar CNPJ.');
    });
}
</script>
