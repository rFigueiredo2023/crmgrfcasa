@props([
    'campos' => [],
    'formSelector' => null,
    'modalSelector' => null
])

{{--
    Componente CampoCnpj - Campo inteligente para consulta de CNPJ

    Propriedades:
    - id: ID único para o campo (obrigatório para evitar conflitos)
    - campos: Array associativo que mapeia IDs de campos do formulário para propriedades da API
    - formSelector: Seletor CSS do formulário pai (opcional)
    - modalSelector: Seletor CSS do modal pai (opcional)
    - value: Valor inicial do campo
    - class: Classes CSS adicionais

    Exemplo de uso:
    <x-campo-cnpj
        id="cnpj-cliente"
        :campos="[
            'razao_social' => 'company.name',
            'email' => 'company.email'
        ]"
        modalSelector="#clienteModal"
    />
--}}

<div class="cnpj-component">
    <label class="form-label" for="{{ $attributes['id'] ?? 'campo-cnpj' }}">CNPJ *</label>
    <input
        type="text"
        class="form-control {{ $attributes['class'] ?? '' }}"
        id="{{ $attributes['id'] ?? 'campo-cnpj' }}"
        name="cnpj"
        required
        placeholder="{{ $attributes['placeholder'] ?? '00.000.000/0000-00' }}"
        {{ $attributes->merge(['placeholder' => null, 'class' => null]) }}
    >
    <div class="invalid-feedback">CNPJ é obrigatório</div>
</div>

@once
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endpush
@endonce

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Namespace único para este componente
    const NAMESPACE = '{{ $attributes['id'] ?? 'campo-cnpj' }}';

    // Seletor do campo CNPJ
    const cnpjInput = document.getElementById('{{ $attributes['id'] ?? 'campo-cnpj' }}');

    // Verificar se o elemento existe
    if (!cnpjInput) {
        console.warn(`[${NAMESPACE}] Campo CNPJ não encontrado`);
        return;
    }

    // Configurar máscara para CNPJ se o jQuery e inputmask estiverem disponíveis
    if (window.jQuery && jQuery().inputmask) {
        try {
            $(cnpjInput).inputmask({
                mask: '99.999.999/9999-99',
                keepStatic: true
            });
        } catch (e) {
            console.warn(`[${NAMESPACE}] Erro ao aplicar máscara no CNPJ:`, e);
        }
    } else {
        // Aplicar máscara via JavaScript puro
        cnpjInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 14) {
                value = value.replace(/(\d{2})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1/$2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
                e.target.value = value;
            }
        });
    }

    // Adicionar evento de blur para consulta automática
    cnpjInput.addEventListener('blur', function() {
        const cnpj = this.value.replace(/\D/g, '');
        if (cnpj.length === 14) {
            consultarCNPJ(cnpj);
        }
    });

    // Função para consultar CNPJ
    function consultarCNPJ(cnpj) {
        console.log(`[${NAMESPACE}] Iniciando consulta de CNPJ:`, cnpj);

        // Verificar CNPJ válido
        if (cnpj.length !== 14) {
            console.warn(`[${NAMESPACE}] CNPJ inválido - comprimento incorreto`);

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'CNPJ Inválido',
                    text: 'O CNPJ deve conter 14 dígitos numéricos.',
                    icon: 'warning'
                });
            }
            return;
        }

        // Mostrar loading se SweetAlert2 estiver disponível
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
        }

        // Configurar timestamp para evitar cache
        const timestamp = new Date().getTime();

        // URL da API com parâmetros para identificação e evitar cache
        const apiUrl = `/consultar-cnpj/${cnpj}?componente=${NAMESPACE}&t=${timestamp}`;

        // Realizar a consulta com melhor tratamento de erros
        fetch(apiUrl)
            .then(response => {
                console.log(`[${NAMESPACE}] Status da resposta:`, response.status);

                // Obter o texto da resposta para análise detalhada
                return response.text().then(text => {
                    console.log(`[${NAMESPACE}] Resposta bruta (resumida):`,
                        text.length > 200 ? text.substring(0, 200) + '...' : text);

                    if (!response.ok) {
                        throw new Error(`Erro HTTP ${response.status}: ${text}`);
                    }

                    // Verificar se é um JSON válido
                    if (!text || text.trim() === '') {
                        throw new Error('Resposta vazia recebida da API');
                    }

                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        console.error(`[${NAMESPACE}] Erro ao parsear JSON:`, e);
                        throw new Error('Resposta inválida recebida da API');
                    }
                });
            })
            .then(data => {
                console.log(`[${NAMESPACE}] Dados recebidos da API:`, data);

                // Fechar o diálogo de carregamento
                if (typeof Swal !== 'undefined' && loadingAlert) {
                    Swal.close();
                }

                if (!data.success) {
                    throw new Error(data.message || 'Erro na consulta do CNPJ');
                }

                // Verificar se existem os dados necessários
                if (!data.data) {
                    throw new Error('Dados incompletos recebidos da API');
                }

                try {
                    // Determinar o contexto para preenchimento de campos
                    let contexto = document;

                    // Se houver um formulário especificado, usar como contexto
                    if ('{{ $formSelector }}') {
                        const form = document.querySelector('{{ $formSelector }}');
                        if (form) {
                            contexto = form;
                        }
                    }
                    // Se houver um modal especificado, usar como contexto
                    else if ('{{ $modalSelector }}') {
                        const modal = document.querySelector('{{ $modalSelector }}');
                        if (modal) {
                            contexto = modal;
                        }
                    }

                    // Iterar sobre os campos definidos no mapeamento
                    @foreach ($campos as $inputField => $apiPath)
                        try {
                            const campoElemento = contexto.querySelector('#{{ $inputField }}');

                            if (campoElemento) {
                                // Parse o caminho da API para acessar dados aninhados
                                let valor = data.data;
                                const caminhos = '{{ $apiPath }}'.split('.');

                                for (const caminho of caminhos) {
                                    if (valor && valor[caminho] !== undefined) {
                                        valor = valor[caminho];
                                    } else {
                                        valor = '';
                                        break;
                                    }
                                }

                                // Tipos especiais de tratamento baseado no campo
                                if ('{{ $inputField }}' === 'endereco' && data.data.address) {
                                    // Construir endereço completo
                                    const end = data.data.address;
                                    let enderecoCompleto = '';

                                    if (end.street) enderecoCompleto += end.street;
                                    if (end.number) enderecoCompleto += end.number ? `, ${end.number}` : '';
                                    if (end.details) enderecoCompleto += end.details ? ` ${end.details}` : '';
                                    if (end.district) enderecoCompleto += end.district ? ` - ${end.district}` : '';

                                    valor = enderecoCompleto;
                                }

                                // Definir o valor no elemento
                                if (campoElemento.tagName === 'SELECT') {
                                    // Para elementos select, procurar a opção correta
                                    const opcoes = campoElemento.options;
                                    for (let i = 0; i < opcoes.length; i++) {
                                        if (opcoes[i].value === valor) {
                                            campoElemento.selectedIndex = i;
                                            break;
                                        }
                                    }
                                } else if (campoElemento.type === 'checkbox') {
                                    // Para checkboxes
                                    campoElemento.checked = !!valor;
                                } else {
                                    // Para inputs normais
                                    campoElemento.value = valor || '';
                                }

                                console.log(`[${NAMESPACE}] Campo #{{ $inputField }} preenchido com:`, valor);

                                // Disparar evento de change para ativar validações ou outros listeners
                                const changeEvent = new Event('change', { bubbles: true });
                                campoElemento.dispatchEvent(changeEvent);
                            } else {
                                console.warn(`[${NAMESPACE}] Campo #{{ $inputField }} não encontrado no contexto`);
                            }
                        } catch (err) {
                            console.error(`[${NAMESPACE}] Erro ao preencher campo #{{ $inputField }}:`, err);
                        }
                    @endforeach

                    // Tratamentos especiais

                    // 1. Inscrição Estadual
                    if (contexto.querySelector('#inscricao_estadual, #ie')) {
                        const ieField = contexto.querySelector('#inscricao_estadual') || contexto.querySelector('#ie');

                        let inscricaoEstadual = '';
                        if (data.data.registrations && data.data.registrations.length > 0) {
                            // Formato CNPJa padrão (array)
                            const ieRegistro = data.data.registrations.find(reg =>
                                reg.type && reg.type.toLowerCase().includes('estadual'));

                            if (ieRegistro && ieRegistro.number) {
                                inscricaoEstadual = ieRegistro.number;
                            }
                        } else if (data.data.registrations && data.data.registrations.BR &&
                                  data.data.registrations.BR.state_registration) {
                            // Formato alternativo (objeto)
                            inscricaoEstadual = data.data.registrations.BR.state_registration;
                        }

                        if (inscricaoEstadual) {
                            ieField.value = inscricaoEstadual;
                            console.log(`[${NAMESPACE}] Inscrição Estadual preenchida:`, inscricaoEstadual);
                        }
                    }

                    // 2. Código IBGE
                    if (contexto.querySelector('#codigo_ibge')) {
                        const ibgeField = contexto.querySelector('#codigo_ibge');

                        // Tentar obter de várias fontes possíveis
                        let codigoIBGE = '';

                        if (data.data.address && data.data.address.ibge_code) {
                            codigoIBGE = data.data.address.ibge_code;
                        } else if (data.data.ibge) {
                            codigoIBGE = data.data.ibge;
                        }

                        if (codigoIBGE) {
                            ibgeField.value = codigoIBGE;
                            console.log(`[${NAMESPACE}] Código IBGE preenchido:`, codigoIBGE);
                        }
                        // Se não encontrou o IBGE, mas tem município e estado, buscar na BrasilAPI
                        else if (data.data.address && data.data.address.city && data.data.address.state) {
                            const cidade = data.data.address.city;
                            const uf = data.data.address.state;

                            console.log(`[${NAMESPACE}] Buscando código IBGE para ${cidade}/${uf} na BrasilAPI`);

                            fetch(`https://brasilapi.com.br/api/ibge/municipios/v1/${uf}`)
                                .then(res => res.json())
                                .then(municipios => {
                                    // Normalizar o nome do município para comparação
                                    const municipioNormalizado = cidade.toUpperCase()
                                        .normalize('NFD').replace(/[\u0300-\u036f]/g, '');

                                    // Procurar o município na lista
                                    const municipioEncontrado = municipios.find(m => {
                                        const nomeNormalizado = m.nome.toUpperCase()
                                            .normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                                        return nomeNormalizado === municipioNormalizado ||
                                               nomeNormalizado.includes(municipioNormalizado) ||
                                               municipioNormalizado.includes(nomeNormalizado);
                                    });

                                    if (municipioEncontrado && municipioEncontrado.codigo_ibge) {
                                        console.log(`[${NAMESPACE}] Código IBGE encontrado:`, municipioEncontrado.codigo_ibge);
                                        ibgeField.value = municipioEncontrado.codigo_ibge;
                                    }
                                })
                                .catch(err => {
                                    console.warn(`[${NAMESPACE}] Erro ao buscar IBGE:`, err);
                                });
                        }
                    }

                    // 3. Preencher município e UF se existirem os campos e os dados
                    if (data.data.address) {
                        // Município
                        if (data.data.address.city && contexto.querySelector('#municipio')) {
                            contexto.querySelector('#municipio').value = data.data.address.city;
                        }

                        // UF
                        if (data.data.address.state && contexto.querySelector('#uf')) {
                            contexto.querySelector('#uf').value = data.data.address.state;
                        }

                        // CEP
                        if (data.data.address.zip && contexto.querySelector('#cep')) {
                            contexto.querySelector('#cep').value = data.data.address.zip;
                        }
                    }

                    // 4. Atividades secundárias (para clientes)
                    if (data.data.sideActivities && contexto.querySelector('#atividades_secundarias')) {
                        try {
                            // Atualizar o campo oculto com JSON
                            if (contexto.querySelector('#atividades_secundarias_json')) {
                                contexto.querySelector('#atividades_secundarias_json').value =
                                    JSON.stringify(data.data.sideActivities);
                            }

                            // Verificar se existe função para exibição de atividades
                            if (typeof window.exibirAtividadesSecundarias === 'function') {
                                window.exibirAtividadesSecundarias(data.data.sideActivities);
                            } else {
                                // Implementação padrão se a função global não existir
                                const container = contexto.querySelector('#atividades_secundarias');
                                container.innerHTML = '';

                                if (!data.data.sideActivities || data.data.sideActivities.length === 0) {
                                    container.innerHTML = '<p class="text-muted mb-0 small">Nenhuma atividade secundária registrada.</p>';
                                    return;
                                }

                                const lista = document.createElement('ul');
                                lista.className = 'list-unstyled mb-0 small';

                                data.data.sideActivities.forEach(atividade => {
                                    const item = document.createElement('li');
                                    item.className = 'mb-1';
                                    item.textContent = atividade.text || atividade;
                                    lista.appendChild(item);
                                });

                                container.appendChild(lista);
                            }
                        } catch (err) {
                            console.error(`[${NAMESPACE}] Erro ao processar atividades secundárias:`, err);
                        }
                    }

                    // 5. Atividade principal
                    if (data.data.mainActivity && contexto.querySelector('#atividade_principal')) {
                        const atividadePrincipal = data.data.mainActivity.text || '';
                        if (atividadePrincipal) {
                            contexto.querySelector('#atividade_principal').value = atividadePrincipal;
                        }
                    }

                    // 6. Porte da empresa
                    if (data.data.company && data.data.company.size &&
                        data.data.company.size.acronym && contexto.querySelector('#porte_empresa')) {
                        const porteValue = data.data.company.size.acronym;
                        const porteSelect = contexto.querySelector('#porte_empresa');

                        // Procurar a opção correspondente
                        const opcoes = porteSelect.options;
                        for (let i = 0; i < opcoes.length; i++) {
                            if (opcoes[i].value === porteValue) {
                                porteSelect.selectedIndex = i;
                                break;
                            }
                        }
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
                    }

                } catch (e) {
                    console.error(`[${NAMESPACE}] Erro ao processar dados da API:`, e);
                    throw e;
                }
            })
            .catch(error => {
                console.error(`[${NAMESPACE}] Erro ao consultar CNPJ:`, error);

                // Garantir que o loading seja fechado
                if (typeof Swal !== 'undefined') {
                    Swal.close();

                    // Mostrar mensagem de erro
                    Swal.fire({
                        title: 'Erro na Consulta',
                        text: error.message || 'Não foi possível consultar o CNPJ.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                } else {
                    alert('Erro na consulta do CNPJ: ' + (error.message || 'Não foi possível consultar o CNPJ.'));
                }
            });
    }
});
</script>
@endpush
