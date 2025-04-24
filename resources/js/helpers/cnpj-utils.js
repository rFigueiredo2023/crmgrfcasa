/**
 * Utilitários para consulta e validação de CNPJ
 */

/**
 * Valida um CNPJ, verificando se possui 14 dígitos
 * @param {string} cnpj - CNPJ a ser validado
 * @return {boolean} - true se válido, false se inválido
 */
export function validarCNPJ(cnpj) {
  // Remove caracteres não numéricos
  cnpj = cnpj.replace(/\D/g, '');

  // Verificar se tem 14 dígitos
  return cnpj.length === 14;
}

/**
 * Formata um CNPJ com a máscara padrão XX.XXX.XXX/XXXX-XX
 * @param {string} cnpj - CNPJ a ser formatado
 * @return {string} - CNPJ formatado ou o valor original se inválido
 */
export function formatarCNPJ(cnpj) {
  // Remove caracteres não numéricos
  cnpj = cnpj.replace(/\D/g, '');

  // Verifica se o CNPJ tem 14 dígitos
  if (cnpj.length !== 14) {
    return cnpj;
  }

  // Aplica a máscara
  return cnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5');
}

/**
 * Consulta o CNPJ na API do sistema
 * @param {string} cnpj - CNPJ a ser consultado
 * @param {function} onSuccess - Callback de sucesso que recebe os dados da API
 * @param {function} onError - Callback de erro que recebe a mensagem de erro
 * @param {function} onLoading - Callback opcional chamado quando a consulta começa
 */
export function consultarCNPJ(cnpj, onSuccess, onError, onLoading = null) {
  // Remove caracteres não numéricos
  cnpj = cnpj.replace(/\D/g, '');

  // Verifica se o CNPJ tem 14 dígitos
  if (cnpj.length !== 14) {
    onError('CNPJ inválido. Deve conter 14 dígitos.');
    return;
  }

  // Notifica que começou o carregamento
  if (onLoading) {
    onLoading();
  }

  // URL da API
  const apiUrl = `/customers/api/consultar-cnpj/${cnpj}`;

  // Log para depuração
  console.log('Consultando CNPJ:', cnpj, 'URL:', apiUrl);

  // Faz a requisição
  fetch(apiUrl)
    .then(response => {
      // Log do status da resposta
      console.log('Status da resposta:', response.status);

      // Obtém o texto da resposta para analisar
      return response.text().then(text => {
        // Log do texto da resposta
        console.log('Resposta bruta:', text);

        if (!response.ok) {
          throw new Error(`Erro ${response.status}: ${text}`);
        }

        // Verificar se o texto é um JSON válido
        if (!text || text.trim() === '') {
          throw new Error('Resposta vazia da API');
        }

        try {
          return JSON.parse(text);
        } catch (e) {
          console.error('Erro ao parsear JSON:', e);
          throw new Error('Resposta inválida da API');
        }
      });
    })
    .then(data => {
      // Log dos dados recebidos
      console.log('Dados do CNPJ recebidos:', data);

      // Verifica se a consulta foi bem-sucedida
      if (!data.success) {
        throw new Error(data.message || 'Erro na consulta do CNPJ');
      }

      // Verifica se existem os dados necessários
      if (!data.data || !data.data.company) {
        throw new Error('Dados incompletos recebidos da API');
      }

      // Chama o callback de sucesso
      onSuccess(data.data);
    })
    .catch(error => {
      // Log do erro
      console.error('Erro na consulta do CNPJ:', error);

      // Chama o callback de erro
      onError(error.message || 'Erro ao consultar CNPJ');
    });
}

/**
 * Extrai os dados cadastrais de uma resposta da API CNPJa
 * @param {Object} data - Dados retornados pela API
 * @return {Object} - Objeto com dados formatados para preenchimento de formulário
 */
export function extrairDadosCadastrais(data) {
  const endereco = data.address || {};

  return {
    razao_social: data.company?.name || '',
    nome_fantasia: data.alias || '',
    inscricao_estadual: data.registrations?.[0]?.number || '',
    endereco: `${endereco.street || ''}, ${endereco.number || ''}`,
    bairro: endereco.district || '',
    municipio: endereco.city || '',
    uf: endereco.state || '',
    cep: endereco.zip || '',
    telefone: data.phones?.[0] ? `${data.phones[0].area || ''}${data.phones[0].number || ''}` : '',
    email: data.emails?.[0]?.address || '',
    situacao: data.status?.text || '',
    cnae_principal: data.mainActivity?.text || ''
  };
}
