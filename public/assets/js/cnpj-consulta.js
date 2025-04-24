/**
 * Script para consulta de CNPJ
 * Independente de outras bibliotecas, exceto SweetAlert2 (opcional)
 */

// Função para tentar várias URLs para uma API, com fallback caso alguma falhe
async function fetchWithFallback(urls, options = {}) {
  // Array para armazenar os erros de cada tentativa
  let errors = [];

  // Tentativa para cada URL fornecida
  for (const url of urls) {
    try {
      console.log('Tentando URL:', url);
      const response = await fetch(url, options);

      // Se a resposta não for ok (2xx), registra o erro e continua para a próxima URL
      if (!response.ok) {
        const text = await response.text();
        const error = new Error(`Erro HTTP ${response.status}: ${text}`);
        error.status = response.status;
        error.url = url;
        error.responseText = text;
        errors.push(error);
        console.warn(`URL ${url} falhou com status ${response.status}`);
        continue; // Tenta a próxima URL
      }

      // Se chegou aqui, a resposta foi bem-sucedida
      return response;
    } catch (err) {
      // Registra erro de rede ou outros
      err.url = url;
      errors.push(err);
      console.warn(`URL ${url} falhou com erro:`, err.message);
    }
  }

  // Se chegou aqui, todas as URLs falharam
  const error = new Error('Todas as URLs de API falharam');
  error.errors = errors;
  throw error;
}

// Função simplificada para buscar CNPJ sem depender de bibliotecas externas
function buscarCNPJSimples(cnpj) {
  console.log('Função buscarCNPJSimples chamada com:', cnpj);
  // Remove caracteres não numéricos
  cnpj = cnpj.replace(/\D/g, '');
  console.log('CNPJ sanitizado:', cnpj);

  if (cnpj.length !== 14) {
    console.log('CNPJ inválido - não tem 14 dígitos');
    // Verifica se SweetAlert2 está disponível
    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: 'CNPJ Inválido',
        text: 'O CNPJ deve conter 14 dígitos numéricos.',
        icon: 'warning'
      });
    } else {
      alert('CNPJ inválido. O CNPJ deve conter 14 dígitos numéricos.');
    }
    return;
  }

  // Feedback visual de carregamento
  let loadingAlert = null;
  if (typeof Swal !== 'undefined') {
    loadingAlert = Swal.fire({
      title: 'Consultando CNPJ',
      text: 'Aguarde enquanto consultamos os dados...',
      icon: 'info',
      allowOutsideClick: false,
      showConfirmButton: false,
      willOpen: () => {
        Swal.showLoading();
      }
    });
  } else {
    console.log('Consultando CNPJ, aguarde...');
  }

  // Lista de possíveis URLs da API, em ordem de prioridade
  const apiUrls = [
    `/customers/api/consultar-cnpj/${cnpj}`,
    `/api/consultar-cnpj/${cnpj}`,
    `/consultar-cnpj/${cnpj}`
  ];

  console.log('URLs a tentar:', apiUrls);

  // Tenta cada URL com fallback
  fetchWithFallback(apiUrls)
    .then(response => {
      console.log('URL bem-sucedida:', response.url);
      console.log('Status da resposta:', response.status);

      // Obtém o texto da resposta para verificação
      return response.text().then(text => {
        // Log do texto da resposta para depuração
        console.log('Resposta bruta da API:', text);

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

      // Fechar o diálogo de carregamento
      if (typeof Swal !== 'undefined' && loadingAlert) {
        Swal.close();
      }

      if (!data.success) {
        throw new Error(data.message || 'Erro na consulta do CNPJ');
      }

      // Verificar se existem os dados necessários
      if (!data.data || !data.data.company) {
        throw new Error('Dados incompletos recebidos da API');
      }

      try {
        // Preenchimento dos campos
        document.querySelectorAll('input[name="razao_social"]').forEach(el => el.value = data.data.company.name || '');

        // Preencher outros campos com verificação de existência
        const preencherCampo = (seletor, valor, fallback = '') => {
          document.querySelectorAll(seletor).forEach(el => el.value = valor || fallback);
        };

        // Endereço pode estar em formatos diferentes dependendo da API
        const endereco = data.data.address;
        if (endereco) {
          preencherCampo('input[name="inscricao_estadual"]', data.data.registrations?.[0]?.number);
          preencherCampo('input[name="endereco"]', `${endereco.street || ''}, ${endereco.number || ''}`);
          preencherCampo('input[name="municipio"]', endereco.city);
          preencherCampo('input[name="uf"]', endereco.state);
          preencherCampo('input[name="cep"]', endereco.zip);
        }

        // Se o IBGE já veio na resposta da API, usar ele
        if (data.data.ibge) {
          preencherCampo('input[name="codigo_ibge"]', data.data.ibge);
        }
        // Se não veio o IBGE, mas veio o município e UF, consultar a BrasilAPI
        else if (endereco?.city && endereco?.state) {
          // Formatar município e UF para o formato esperado pela BrasilAPI
          const municipio = (endereco.city || '').toUpperCase().replace(/\s+/g, '-');
          const uf = (endereco.state || '').toUpperCase();

          console.log('Consultando BrasilAPI para código IBGE:', municipio, uf);

          // Buscar IBGE com a BrasilAPI
          fetch(`https://brasilapi.com.br/api/ibge/municipios/v1/${uf}`)
            .then(res => {
              if (!res.ok) {
                throw new Error(`Erro ${res.status} ao consultar BrasilAPI`);
              }
              return res.json();
            })
            .then(municipios => {
              // BrasilAPI retorna uma lista de municípios do estado
              // Precisamos encontrar o que corresponde ao nome do município
              const municipioNormalizado = endereco.city.toUpperCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');

              // Procurar o município na lista retornada
              const municipioEncontrado = municipios.find(m => {
                const nomeNormalizado = m.nome.toUpperCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                return nomeNormalizado === municipioNormalizado ||
                       nomeNormalizado.includes(municipioNormalizado) ||
                       municipioNormalizado.includes(nomeNormalizado);
              });

              if (municipioEncontrado && municipioEncontrado.codigo_ibge) {
                console.log('Código IBGE encontrado:', municipioEncontrado.codigo_ibge);
                preencherCampo('input[name="codigo_ibge"]', municipioEncontrado.codigo_ibge);
              } else {
                console.warn('Não foi possível encontrar o código IBGE para', endereco.city, endereco.state);
              }
            })
            .catch(err => {
              console.warn('Não foi possível buscar o código IBGE na BrasilAPI:', err);
            });
        }

        // Mostrar mensagem de sucesso
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            title: 'Sucesso!',
            text: 'Dados do CNPJ carregados com sucesso',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
          });
        } else {
          console.log('Dados do CNPJ carregados com sucesso');
        }
      } catch (e) {
        console.error('Erro ao processar dados da API:', e);
        throw e;
      }
    })
    .catch(error => {
      console.error('Erro ao consultar CNPJ:', error);

      // Mensagem de erro detalhada para debug
      let mensagemErro = error.message || 'Não foi possível consultar o CNPJ.';

      // Se houver múltiplos erros, mostrar detalhes de cada tentativa
      if (error.errors) {
        mensagemErro += '\n\nDetalhes das tentativas:';
        error.errors.forEach((err, index) => {
          mensagemErro += `\n${index+1}. URL: ${err.url} - ${err.message}`;
        });
      }

      // Fechar o diálogo de carregamento e mostrar o erro
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: 'Erro na Consulta',
          text: mensagemErro,
          icon: 'error',
          confirmButtonText: 'OK'
        });
      } else {
        alert('Erro na consulta do CNPJ: ' + mensagemErro);
      }
    });
}

// Configurar evento de escuta para campos de CNPJ quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
  // Configura todos os campos de CNPJ na página
  try {
    document.querySelectorAll('input[name="cnpj"]').forEach(function(input) {
      input.addEventListener('blur', function() {
        if (this.value && this.value.length > 0) {
          buscarCNPJSimples(this.value);
        }
      });
    });

    // Configuração para jQuery se disponível (compatibilidade com modais dinâmicos)
    if (typeof jQuery !== 'undefined') {
      jQuery(document).on('blur', 'input[name="cnpj"]', function() {
        if (this.value && this.value.length > 0) {
          buscarCNPJSimples(this.value);
        }
      });
    }

    console.log('Configuração de busca CNPJ concluída com sucesso');
  } catch (e) {
    console.error('Erro ao configurar eventos de CNPJ:', e);
  }
});
