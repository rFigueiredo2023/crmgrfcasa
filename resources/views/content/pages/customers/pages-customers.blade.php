{{-- View de Pages customers relacionada a content/pages/customers --}}
@extends('layouts/horizontalLayout')

@section('title', 'Cadastros')

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
        'resources/assets/vendor/libs/select2/select2.scss',
        'resources/assets/vendor/libs/@form-validation/form-validation.scss'
    ])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/moment/moment.js',
        'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js',
        'resources/assets/vendor/libs/select2/select2.js',
        'resources/assets/vendor/libs/@form-validation/popular.js',
        'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
        'resources/assets/vendor/libs/@form-validation/auto-focus.js',
        'resources/assets/vendor/libs/cleavejs/cleave.js',
        'resources/assets/vendor/libs/cleavejs/cleave-phone.js'
    ])
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <!-- Nav tabs -->
            <ul class="nav nav-pills mb-3" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-clientes" type="button" role="tab">
                        <i class='bx bx-user me-1'></i> Clientes
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-transportadoras" type="button" role="tab">
                        <i class='bx bx-truck me-1'></i> Transportadoras
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-veiculos" type="button" role="tab">
                        <i class='bx bx-car me-1'></i> Veículos
                    </button>
                </li>
                @if(auth()->user()->isAdmin())
                <!-- Removido a pílula "Segmentos" -->
                <!-- Removido a pílula "Admin" -->
                @endif
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div class="tab-pane fade show active" id="tab-clientes" role="tabpanel">
                    @include('components.tabela-clientes')
                </div>
                <div class="tab-pane fade" id="tab-transportadoras" role="tabpanel">
                    @include('components.tabela-transportadoras')
                </div>
                <div class="tab-pane fade" id="tab-veiculos" role="tabpanel">
                    @include('components.tabela-veiculos')
                </div>
                @if(auth()->user()->isAdmin())
                <!-- Removido o painel de segmentos -->
                @endif
            </div>
        </div>
    </div>
@endsection

@section('page-script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Máscara para CNPJ no formulário de adição
        const cnpjInput = document.getElementById('add_cnpj');
        if (cnpjInput) {
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

        // Máscara para telefone no formulário de adição
        const telefoneInput = document.getElementById('add_telefone');
        if (telefoneInput) {
            telefoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 11) {
                    value = value.replace(/(\d{2})(\d)/, '($1) $2');
                    value = value.replace(/(\d{5})(\d)/, '$1-$2');
                    e.target.value = value;
                }
            });
        }

        // Máscara para telefone2 no formulário de adição
        const telefone2Input = document.getElementById('add_telefone2');
        if (telefone2Input) {
            telefone2Input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 11) {
                    value = value.replace(/(\d{2})(\d)/, '($1) $2');
                    value = value.replace(/(\d{5})(\d)/, '$1-$2');
                    e.target.value = value;
                }
            });
        }

        // Máscara para telefone2 no formulário de edição
        const editTelefone2Input = document.getElementById('edit_telefone2');
        if (editTelefone2Input) {
            editTelefone2Input.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 11) {
                    value = value.replace(/(\d{2})(\d)/, '($1) $2');
                    value = value.replace(/(\d{5})(\d)/, '$1-$2');
                    e.target.value = value;
                }
            });
        }

        // Máscara para CEP no formulário de adição
        const cepInput = document.getElementById('add_cep');
        if (cepInput) {
            cepInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 8) {
                    value = value.replace(/(\d{5})(\d)/, '$1-$2');
                    e.target.value = value;
                }
            });

            // Buscar endereço ao preencher CEP (Adição)
            cepInput.addEventListener('blur', function() {
                const cep = this.value.replace(/\D/g, '');
                if (cep.length === 8) {
                    fetch(`https://viacep.com.br/ws/${cep}/json/`)
                        .then(response => response.json())
                        .then(data => {
                            if (!data.erro) {
                                document.getElementById('add_endereco').value = data.logradouro || '';
                                document.getElementById('add_municipio').value = data.localidade || '';
                                document.getElementById('add_uf').value = data.uf || '';
                                document.getElementById('add_codigo_ibge').value = data.ibge || '';
                            }
                        })
                        .catch(error => console.error('Erro ao buscar CEP:', error));
                }
            });
        }

        // Máscara para CEP no formulário de edição
        const editCepInput = document.getElementById('edit_cep');
        if (editCepInput) {
            editCepInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 8) {
                    value = value.replace(/(\d{5})(\d)/, '$1-$2');
                    e.target.value = value;
                }
            });

            // Buscar endereço ao preencher CEP (Edição)
            editCepInput.addEventListener('blur', function() {
                const cep = this.value.replace(/\D/g, '');
                if (cep.length === 8) {
                    fetch(`https://viacep.com.br/ws/${cep}/json/`)
                        .then(response => response.json())
                        .then(data => {
                            if (!data.erro) {
                                document.getElementById('edit_endereco').value = data.logradouro || '';
                                document.getElementById('edit_municipio').value = data.localidade || '';
                                document.getElementById('edit_uf').value = data.uf || '';
                                document.getElementById('edit_codigo_ibge').value = data.ibge || '';
                            }
                        })
                        .catch(error => console.error('Erro ao buscar CEP:', error));
                }
            });
        }

        // Modal de Transportadora
        const modalEditTransportadora = document.getElementById('modalEditTransportadora');
        if (modalEditTransportadora) {
            modalEditTransportadora.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const transportadoraId = button.getAttribute('data-transportadora-id');
                const form = this.querySelector('#formEditTransportadora');

                if (!transportadoraId) {
                    console.error('ID da transportadora não encontrado');
                    return;
                }

                // Atualiza a action do formulário
                form.action = `/customers/transportadoras/${transportadoraId}`;

                // Busca os dados da transportadora via AJAX
                fetch(`/customers/transportadoras/${transportadoraId}/edit`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erro na resposta da rede');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (!data) {
                            throw new Error('Dados da transportadora não encontrados');
                        }
                        // Preenche os campos do formulário
                        form.querySelector('#edit_trans_razao_social').value = data.razao_social || '';
                        form.querySelector('#edit_trans_cnpj').value = data.cnpj || '';
                        form.querySelector('#edit_trans_ie').value = data.ie || '';
                        form.querySelector('#edit_trans_endereco').value = data.endereco || '';
                        form.querySelector('#edit_trans_codigo_ibge').value = data.codigo_ibge || '';
                        form.querySelector('#edit_trans_telefone').value = data.telefone || '';
                        form.querySelector('#edit_trans_email').value = data.email || '';
                        form.querySelector('#edit_trans_contato').value = data.contato || '';
                    })
                    .catch(error => {
                        console.error('Erro ao carregar dados da transportadora:', error);
                        alert('Erro ao carregar dados da transportadora');
                    });
            });
        }

        // Função para exibir atividades secundárias
        function exibirAtividadesSecundarias(atividades, containerId, inputId) {
            const container = document.getElementById(containerId);
            container.innerHTML = '';

            // Atualizar o campo oculto com o JSON das atividades
            if (inputId) {
                document.getElementById(inputId).value = JSON.stringify(atividades);
            }

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

        // Função para buscar CNPJ e preencher dados
        window.buscarCNPJSimples = function(cnpj) {
            // Remove caracteres não numéricos
            cnpj = cnpj.replace(/\D/g, '');

            if (cnpj.length !== 14) {
                console.warn('CNPJ Inválido - não tem 14 dígitos');
                return;
            }

            // URL do proxy Laravel
            const apiUrl = `/consultar-cnpj/${cnpj}`;

            // Iniciar um diálogo de carregamento
            const loadingAlert = Swal.fire({
                title: 'Consultando CNPJ...',
                html: 'Por favor, aguarde enquanto consultamos os dados do CNPJ.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Requisição com fetch
            fetch(apiUrl)
                .then(response => {
                    // Primeiro obtém o texto da resposta para verificação
                    return response.text().then(text => {
                        if (!response.ok) {
                            throw new Error(`Erro ${response.status}: ${text}`);
                        }

                        // Verificar se o texto é um JSON válido antes de parsear
                        if (!text || text.trim() === '') {
                            throw new Error('Resposta vazia recebida da API');
                        }

                        try {
                            return JSON.parse(text);
                        } catch (e) {
                            throw new Error('Resposta inválida recebida da API');
                        }
                    });
                })
                .then(data => {
                    // Fechar o diálogo de carregamento
                    loadingAlert.close();

                    if (!data.success) {
                        throw new Error(data.message || 'Erro na consulta do CNPJ');
                    }

                    // Mostrar mensagem de sucesso
                    Swal.fire({
                        title: 'Consulta Realizada',
                        text: 'Dados do CNPJ obtidos com sucesso!',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });

                    try {
                        // Verificar se estamos no modal de transportadora
                        const isTransportadoraModal = document.querySelector('#modalAddTransportadora.show');
                        const modalTransportadora = document.getElementById('modalAddTransportadora');

                        // Se for o modal de transportadora
                        if (modalTransportadora &&
                            (isTransportadoraModal || modalTransportadora.classList.contains('show'))) {
                            console.log('Preenchendo dados de transportadora');

                            // Pegar os elementos do formulário
                            const razaoSocialInput = modalTransportadora.querySelector('input[name="razao_social"]');
                            const inscricaoEstadualInput = modalTransportadora.querySelector('input[name="inscricao_estadual"]');
                            const enderecoInput = modalTransportadora.querySelector('input[name="endereco"]');
                            const codigoIbgeInput = modalTransportadora.querySelector('input[name="codigo_ibge"]');

                            console.log('Elementos encontrados:', {
                                razaoSocial: !!razaoSocialInput,
                                inscricaoEstadual: !!inscricaoEstadualInput,
                                endereco: !!enderecoInput,
                                codigoIbge: !!codigoIbgeInput
                            });

                            // Preencher os campos com os dados da API
                            if (razaoSocialInput && data.data.company) {
                                razaoSocialInput.value = data.data.company.name || '';
                                console.log('Razão social preenchida:', data.data.company.name);
                            }

                            // Preencher inscrição estadual
                            if (inscricaoEstadualInput && data.data.registrations && data.data.registrations.BR) {
                                inscricaoEstadualInput.value = data.data.registrations.BR.state_registration || '';
                                console.log('IE preenchida:', data.data.registrations.BR.state_registration);
                            }

                            // Preencher endereço
                            if (enderecoInput && data.data.address) {
                                const address = data.data.address;
                                let enderecoCompleto = `${address.street || ''}, ${address.number || ''} ${address.details || ''}`;
                                enderecoCompleto += ` - ${address.district || ''}, ${address.city || ''} - ${address.state || ''}`;
                                enderecoCompleto += ` CEP: ${address.zip || ''}`;

                                enderecoInput.value = enderecoCompleto;
                                console.log('Endereço preenchido:', enderecoCompleto);
                            }

                            // Preencher código IBGE
                            if (codigoIbgeInput && data.data.address && data.data.address.ibge_code) {
                                codigoIbgeInput.value = data.data.address.ibge_code;
                                console.log('Código IBGE preenchido:', data.data.address.ibge_code);
                            }

                            // Não preencher campos que devem ser preenchidos pelo usuário:
                            // - telefone
                            // - celular
                            // - email
                            // - contato
                            // - observacoes

                            return; // Encerra aqui para não executar o código de clientes
                        }

                        // Código para cliente (existente)
                        // Identificar qual modal está ativo (adicionar ou editar)
                        const isAddModal = document.getElementById('modalAddCliente').classList.contains('show');
                        const prefix = isAddModal ? 'add_' : 'edit_';

                        // Preencher campos do formulário ativo
                        const formId = isAddModal ? 'formAddCliente' : 'formEditCliente';
                        const form = document.getElementById(formId);

                        if (!form) {
                            throw new Error('Formulário não encontrado');
                        }

                        // Log para depuração
                        console.log('Modal ativo:', isAddModal ? 'Adicionar Cliente' : 'Editar Cliente');
                        console.log('Campo IE ID:', `#${prefix}ie`);
                        console.log('Elemento IE existe:', !!form.querySelector(`#${prefix}ie`));

                        // Função segura para preencher campos, verificando se eles existem
                        const preencherCampo = (seletor, valor, fallback = '') => {
                            const elemento = form.querySelector(seletor);
                            if (elemento) {
                                elemento.value = valor || fallback;
                            } else {
                                console.warn(`Elemento não encontrado: ${seletor}`);
                            }
                        };

                        // Preencher os campos básicos
                        if (data.data.company) {
                            preencherCampo(`#${prefix}razao_social`, data.data.company.name);

                            // Preencher porte da empresa se disponível
                            if (data.data.company.size && data.data.company.size.acronym) {
                                const porteSelect = form.querySelector(`#${prefix}porte_empresa`);
                                if (porteSelect) {
                                    porteSelect.value = data.data.company.size.acronym;
                                }
                            }
                        }

                        // Tentar preencher inscrição estadual (várias fontes possíveis)
                        // Método 1: Campo registrations
                        if (data.data.registrations && data.data.registrations.length > 0) {
                            console.log('Registrations encontradas:', data.data.registrations);
                            // Procurar a inscrição estadual do estado atual ou qualquer uma disponível
                            const estadoUF = (document.querySelector(`#${prefix}uf`) || {value: ''}).value;
                            console.log('Estado atual:', estadoUF);

                            // Primeiro tentar encontrar a IE do estado atual
                            let inscricaoEstadual = data.data.registrations.find(reg => reg.state === estadoUF);

                            // Se não encontrar para o estado atual, usar qualquer uma disponível
                            if (!inscricaoEstadual && data.data.registrations.length > 0) {
                                inscricaoEstadual = data.data.registrations[0];
                                console.log('Usando primeira inscrição disponível:', inscricaoEstadual);
                            }

                            if (inscricaoEstadual) {
                                preencherCampo(`#${prefix}ie`, inscricaoEstadual.number);
                                console.log('IE encontrada:', inscricaoEstadual.number, 'Estado:', inscricaoEstadual.state);
                            } else {
                                console.log('Nenhuma inscrição estadual encontrada nos registros');
                            }
                        }
                        // Método 2: Verificar se há um campo específico na resposta
                        else if (data.data.stateRegistration) {
                            preencherCampo(`#${prefix}ie`, data.data.stateRegistration);
                            console.log('IE encontrada em stateRegistration:', data.data.stateRegistration);
                        }
                        // Método 3: Consultar diretamente da Receita Federal ou API secundária
                        else {
                            console.log('Tentando obter IE de fonte secundária...');
                            // Consultar a inscrição estadual por uma API secundária usando o CNPJ
                            const cnpjSemPontuacao = (data.data.taxId || '').replace(/\D/g, '');
                            if (cnpjSemPontuacao) {
                                const url = `/api/consultar-ie/${cnpjSemPontuacao}`;
                                console.log(`Consultando IE via: ${url}`);

                                // Opcionalmente fazer uma nova consulta para obter a IE
                                // Este é um exemplo - a implementação real depende da existência dessa API
                                /*
                                fetch(url)
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success && data.inscricaoEstadual) {
                                            preencherCampo(`#${prefix}ie`, data.inscricaoEstadual);
                                        }
                                    })
                                    .catch(error => console.error('Erro ao consultar IE:', error));
                                */
                            }

                            // Exibir os dados completos da API no console para debugging
                            console.log('Dados completos da API:', data.data);
                        }

                        // Gerenciar campo SUFRAMA
                        const campoSuframa = form.querySelector(`#${prefix}suframa`);
                        if (campoSuframa) {
                            if (data.data.suframa && data.data.suframa.length > 0) {
                                // Preenche o campo SUFRAMA com o valor da API
                                campoSuframa.value = data.data.suframa[0].number || '';
                                campoSuframa.readOnly = false;
                                campoSuframa.disabled = false;
                                campoSuframa.style.backgroundColor = '';
                                campoSuframa.style.cursor = 'text';
                            } else {
                                // Se não houver SUFRAMA, limpa e desabilita o campo
                                campoSuframa.value = '';
                                campoSuframa.readOnly = true;
                                campoSuframa.disabled = true;
                                campoSuframa.style.backgroundColor = '#e9ecef'; // Cinza escuro padrão do Bootstrap
                                campoSuframa.style.cursor = 'not-allowed';
                            }
                        }

                        if (data.data.address) {
                            const endereco = data.data.address;
                            preencherCampo(`#${prefix}endereco`, `${endereco.street || ''}, ${endereco.number || ''}`);
                            preencherCampo(`#${prefix}municipio`, endereco.city);
                            preencherCampo(`#${prefix}uf`, endereco.state);
                            preencherCampo(`#${prefix}cep`, endereco.zip);
                            preencherCampo(`#${prefix}codigo_ibge`, endereco.municipality);
                        }

                        // Preencher atividade principal
                        if (data.data.mainActivity) {
                            preencherCampo(`#${prefix}atividade_principal`, data.data.mainActivity.text);
                        }

                        // Preencher atividades secundárias
                        if (data.data.sideActivities) {
                            const containerId = `${prefix}atividades_secundarias`;
                            const inputId = `${prefix}atividades_secundarias_json`;

                            // Verificar se os elementos existem antes de chamar a função
                            const container = document.getElementById(containerId);
                            const inputElement = document.getElementById(inputId);

                            if (container) {
                                // Limpar container
                                container.innerHTML = '';

                                // Atualizar campo oculto se existir
                                if (inputElement) {
                                    inputElement.value = JSON.stringify(data.data.sideActivities);
                                }

                                // Exibir atividades
                                if (!data.data.sideActivities || data.data.sideActivities.length === 0) {
                                    container.innerHTML = '<p class="text-muted mb-0 small">Nenhuma atividade secundária registrada.</p>';
                                } else {
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
                            } else {
                                console.warn(`Container de atividades secundárias não encontrado: ${containerId}`);
                            }
                        }
                    } catch (error) {
                        console.error('Erro ao processar dados:', error);
                        throw new Error('Erro ao processar os dados do CNPJ: ' + error.message);
                    }
                })
                .catch(error => {
                    // Fechar o diálogo de carregamento e mostrar o erro
                    loadingAlert.close();

                    Swal.fire({
                        title: 'Erro na Consulta',
                        text: error.message || 'Não foi possível consultar o CNPJ.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
        };

        // Função genérica para carregar dados em um modal
        function setupModalEdit(modalId, route) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const form = this.querySelector('form');

                    form.action = route.replace('__id__', id);

                    fetch(`${route.replace('__id__', id)}/edit`)
                        .then(response => {
                            if (!response.ok) throw new Error('Erro na resposta da rede');
                            return response.json();
                        })
                        .then(data => {
                            if (!data) throw new Error('Dados não encontrados');

                            // Preenche todos os campos do formulário
                            Object.keys(data).forEach(key => {
                                const input = form.querySelector(`[name="${key}"]`);
                                if (input) input.value = data[key] || '';
                            });
                        })
                        .catch(error => {
                            console.error('Erro ao carregar dados:', error);
                            alert('Erro ao carregar dados');
                        });
                });
            }
        }

        // Inicializa os modais
        setupModalEdit('modalEditTransportadora', '/customers/transportadoras/__id__');
        setupModalEdit('modalEditVeiculo', '/customers/veiculos/__id__');

        // Inicialização específica para o modal de edição de cliente
        const modalEditCliente = document.getElementById('modalEditCliente');
        if (modalEditCliente) {
            modalEditCliente.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const clienteId = button.getAttribute('data-id');
                const form = this.querySelector('#formEditCliente');

                if (!clienteId) {
                    console.error('ID do cliente não encontrado');
                    return;
                }

                // Feedback visual de carregamento
                const modalBody = this.querySelector('.modal-body');
                const originalContent = modalBody.innerHTML;
                modalBody.innerHTML = `
                    <div class="text-center p-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-3">Carregando dados do cliente...</p>
                    </div>
                `;

                // Atualiza a action do formulário
                form.action = `/customers/clientes/${clienteId}`;

                // Busca os dados do cliente via AJAX
                fetch(`/customers/clientes/${clienteId}/edit`)
                    .then(response => {
                        console.log('Status da resposta:', response.status);
                        if (!response.ok) {
                            return response.text().then(text => {
                                throw new Error(`Erro na resposta da rede: ${response.status} - ${text}`);
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Restaura o conteúdo original do modal
                        modalBody.innerHTML = originalContent;

                        console.log('Dados recebidos:', data);
                        if (!data) {
                            throw new Error('Dados do cliente não encontrados');
                        }

                        // Preenche todos os campos do formulário
                        form.querySelector('#edit_razao_social').value = data.razao_social || '';
                        form.querySelector('#edit_cnpj').value = data.cnpj || '';
                        form.querySelector('#edit_ie').value = data.inscricao_estadual || '';

                        // Tratamento específico para o campo SUFRAMA
                        const campoSuframa = form.querySelector('#edit_suframa');
                        if (campoSuframa) {
                            if (data.suframa) {
                                campoSuframa.value = data.suframa;
                                campoSuframa.readOnly = false;
                                campoSuframa.disabled = false;
                                campoSuframa.style.backgroundColor = '';
                                campoSuframa.style.cursor = 'text';
                            } else {
                                campoSuframa.value = '';
                                campoSuframa.readOnly = true;
                                campoSuframa.disabled = true;
                                campoSuframa.style.backgroundColor = '#e9ecef';
                                campoSuframa.style.cursor = 'not-allowed';
                            }
                        }

                        form.querySelector('#edit_email').value = data.email || '';
                        form.querySelector('#edit_cep').value = data.cep || '';
                        form.querySelector('#edit_endereco').value = data.endereco || '';
                        form.querySelector('#edit_codigo_ibge').value = data.codigo_ibge || '';
                        form.querySelector('#edit_municipio').value = data.municipio || '';
                        form.querySelector('#edit_uf').value = data.uf || '';
                        form.querySelector('#edit_telefone').value = data.telefone || '';
                        form.querySelector('#edit_telefone2').value = data.telefone2 || '';
                        form.querySelector('#edit_contato').value = data.contato || '';
                        form.querySelector('#edit_site').value = data.site || '';

                        // Atividade principal
                        if (form.querySelector('#edit_atividade_principal')) {
                            form.querySelector('#edit_atividade_principal').value = data.atividade_principal || '';
                        }

                        // Segmento (select)
                        if (data.segmento_id && form.querySelector('#edit_segmento_id')) {
                            form.querySelector('#edit_segmento_id').value = data.segmento_id;
                        }

                        // Atividades secundárias (JSON)
                        if (data.atividades_secundarias && form.querySelector('#edit_atividades_secundarias_json')) {
                            form.querySelector('#edit_atividades_secundarias_json').value = data.atividades_secundarias;

                            // Se tiver a função para exibir as atividades, use-a
                            if (typeof exibirAtividadesSecundarias === 'function') {
                                try {
                                    const atividades = typeof data.atividades_secundarias === 'string'
                                        ? JSON.parse(data.atividades_secundarias)
                                        : data.atividades_secundarias;

                                    exibirAtividadesSecundarias(
                                        atividades,
                                        'edit_atividades_secundarias',
                                        'edit_atividades_secundarias_json'
                                    );
                                } catch (e) {
                                    console.error('Erro ao processar atividades secundárias:', e);
                                }
                            }
                        }

                        // Preencher dados adicionais da API CNPJa
                        const preencher = (id, valor, formatador = null) => {
                            const elemento = form.querySelector(id);
                            if (elemento && valor !== undefined) {
                                elemento.value = formatador ? formatador(valor) : valor;
                            }
                        };

                        // Dados básicos da API CNPJa
                        preencher('#edit_nome_fantasia', data.nome_fantasia);
                        preencher('#edit_fundacao', data.fundacao, val => val ? new Date(val).toLocaleDateString('pt-BR') : '');
                        preencher('#edit_situacao', data.situacao);
                        preencher('#edit_data_situacao', data.data_situacao, val => val ? new Date(val).toLocaleDateString('pt-BR') : '');
                        preencher('#edit_natureza_juridica', data.natureza_juridica);
                        preencher('#edit_porte', data.porte);
                        preencher('#edit_capital_social', data.capital_social, val => val ?
                            parseFloat(val).toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'}) : '');
                        preencher('#edit_simples_nacional', data.simples_nacional, val => val ? 'Sim' : 'Não');

                        // Preencher sócios
                        const sociosContainer = form.querySelector('#edit_socios_container');
                        if (sociosContainer) {
                            // Limpar container
                            sociosContainer.innerHTML = '';

                            // Verificar se há sócios
                            if (data.lista_socios) {
                                let socios;
                                try {
                                    socios = typeof data.lista_socios === 'string' ?
                                        JSON.parse(data.lista_socios) : data.lista_socios;

                                    if (socios && socios.length > 0) {
                                        const lista = document.createElement('ul');
                                        lista.className = 'list-unstyled mb-0 small';

                                        socios.forEach(socio => {
                                            const item = document.createElement('li');
                                            item.className = 'mb-2';
                                            item.innerHTML = `<strong>${socio.nome || 'N/A'}</strong> - ${socio.funcao || 'N/A'} (${socio.idade || 'N/A'})`;
                                            lista.appendChild(item);
                                        });

                                        sociosContainer.appendChild(lista);
                                    } else {
                                        sociosContainer.innerHTML = '<p class="text-muted mb-0 small">Nenhum sócio registrado.</p>';
                                    }
                                } catch (e) {
                                    console.error('Erro ao processar lista de sócios:', e);
                                    sociosContainer.innerHTML = '<p class="text-muted mb-0 small">Erro ao processar sócios.</p>';
                                }
                            } else if (data.socio_principal) {
                                // Se não tiver lista mas tiver sócio principal
                                const p = document.createElement('p');
                                p.className = 'mb-0 small';
                                p.innerHTML = `<strong>${data.socio_principal}</strong> - ${data.funcao_socio || 'N/A'}`;
                                sociosContainer.appendChild(p);
                            } else {
                                sociosContainer.innerHTML = '<p class="text-muted mb-0 small">Nenhum sócio registrado.</p>';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao carregar dados do cliente:', error);

                        // Restaura o conteúdo original e mostra mensagem de erro
                        modalBody.innerHTML = originalContent;

                        // Mostra mensagem de erro no modal
                        Swal.fire({
                            title: 'Erro!',
                            text: 'Erro ao carregar dados do cliente: ' + error.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    });
            });
        }

        // Código para o modal de adicionar segmento
        const formAddSegmento = document.querySelector('#modalAddSegmento form');
        if (formAddSegmento) {
            formAddSegmento.addEventListener('submit', function(e) {
                e.preventDefault();

                fetch(this.action, {
                    method: 'POST',
                    body: new FormData(this),
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Fechar modal
                        bootstrap.Modal.getInstance(document.getElementById('modalAddSegmento')).hide();

                        // Mostrar mensagem de sucesso
                        Swal.fire({
                            title: 'Sucesso!',
                            text: 'Segmento criado com sucesso!',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            // Recarregar a página para mostrar o novo segmento
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Erro!',
                            text: data.message || 'Erro ao criar segmento.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                })
                .catch(error => {
                    console.error('Erro:', error);
                    Swal.fire({
                        title: 'Erro!',
                        text: 'Ocorreu um erro ao processar sua solicitação.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                });
            });
        }

        // Adiciona o evento de busca de CNPJ a todos os campos de CNPJ na página
        document.querySelectorAll('input[name="cnpj"]').forEach(function(input) {
            input.addEventListener('blur', function() {
                if (this.value && this.value.length > 0) {
                    try {
                        // Verifica se a função existe e a chama
                        if (typeof buscarCNPJSimples === 'function') {
                            buscarCNPJSimples(this.value);
                        } else {
                            console.warn('Função buscarCNPJSimples não encontrada');
                        }
                    } catch (e) {
                        console.error('Erro ao chamar buscarCNPJSimples:', e);
                    }
                }
            });
        });

        // Adiciona a função confirmarExclusao ao escopo global
        window.confirmarExclusao = function(id) {
            if (confirm('Tem certeza que deseja excluir este cliente?')) {
                // Criar um form dinâmico para enviar a requisição DELETE
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/customers/clientes/${id}`;

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';

                const tokenInput = document.createElement('input');
                tokenInput.type = 'hidden';
                tokenInput.name = '_token';
                tokenInput.value = document.querySelector('meta[name="csrf-token"]').content;

                form.appendChild(methodInput);
                form.appendChild(tokenInput);
                document.body.appendChild(form);
                form.submit();
            }
        };

        // Adicionar evento para detectar quando o modal de transportadora for aberto
        const modalAddTransportadora = document.getElementById('modalAddTransportadora');
        if (modalAddTransportadora) {
            modalAddTransportadora.addEventListener('shown.bs.modal', function() {
                console.log('Modal de transportadora aberto');

                // Mostrar estrutura HTML do modal
                console.log('Estrutura do modal:', modalAddTransportadora.innerHTML);

                // Detectar campo de CNPJ e adicionar evento de blur
                const cnpjInput = modalAddTransportadora.querySelector('input[name="cnpj"]');
                if (cnpjInput) {
                    console.log('Campo CNPJ encontrado:', cnpjInput);

                    // Adicionar evento de blur se ainda não tiver
                    if (!cnpjInput.hasAttribute('data-consulta-attached')) {
                        cnpjInput.setAttribute('data-consulta-attached', 'true');
                        cnpjInput.addEventListener('blur', function() {
                            console.log('Evento blur do CNPJ acionado');
                            if (typeof buscarCNPJSimples === 'function') {
                                buscarCNPJSimples(this.value);
                            } else {
                                console.warn('Função buscarCNPJSimples não encontrada');
                            }
                        });
                        console.log('Evento de consulta CNPJ adicionado');
                    }
                } else {
                    console.warn('Campo CNPJ não encontrado no modal');
                }
            });
        } else {
            console.warn('Modal de transportadora não encontrado');
        }
    });
</script>
@endsection

@push('modals')
    @include('components.modal-historico-cliente')
    <!-- Modal de Adição -->
    <div class="modal fade" id="modalAddCliente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-simple">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Adicionar Cliente</h3>
                        <p class="text-muted">Preencha os dados do novo cliente</p>
                    </div>

                    <form id="formAddCliente" class="row g-3" action="{{ route('clientes.store') }}" method="POST">
                        @csrf
                        <input type="hidden" id="atividades_secundarias_json" name="atividades_secundarias">

                        <div class="col-md-6">
                            <label class="form-label" for="add_razao_social">Razão Social</label>
                            <input type="text" class="form-control" id="add_razao_social" name="razao_social" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="add_cnpj">CNPJ</label>
                            <input type="text" class="form-control" id="add_cnpj" name="cnpj" required>
                        </div>

                        <!-- Atividade Principal -->
                        <div class="col-md-12">
                            <label class="form-label" for="add_atividade_principal">Atividade Principal</label>
                            <input type="text" class="form-control" id="add_atividade_principal" name="atividade_principal" readonly>
                        </div>

                        <!-- Atividades Secundárias -->
                        <div class="col-md-12">
                            <label class="form-label">Atividades Secundárias</label>
                            <div id="add_atividades_secundarias" class="border p-2 rounded bg-light" style="min-height: 40px; max-height: 120px; overflow-y: auto;">
                                <p class="text-muted mb-0 small">As atividades secundárias serão exibidas aqui.</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="add_ie">Inscrição Estadual</label>
                            <input type="text" class="form-control" id="add_ie" name="inscricao_estadual">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="add_suframa">SUFRAMA</label>
                            <input type="text" class="form-control" id="add_suframa" name="suframa">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="add_email">Email</label>
                            <input type="email" class="form-control" id="add_email" name="email">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="add_cep">CEP</label>
                            <input type="text" class="form-control" id="add_cep" name="cep">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label" for="add_endereco">Endereço</label>
                            <input type="text" class="form-control" id="add_endereco" name="endereco" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="add_codigo_ibge">Código IBGE</label>
                            <input type="text" class="form-control" id="add_codigo_ibge" name="codigo_ibge" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="add_municipio">Município</label>
                            <input type="text" class="form-control" id="add_municipio" name="municipio" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="add_uf">UF</label>
                            <input type="text" class="form-control" id="add_uf" name="uf" maxlength="2" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="add_telefone">Telefone</label>
                            <input type="text" class="form-control" id="add_telefone" name="telefone" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="add_telefone2">Telefone 2</label>
                            <input type="text" class="form-control" id="add_telefone2" name="telefone2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="add_contato">Contato</label>
                            <input type="text" class="form-control" id="add_contato" name="contato" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="add_site">Site</label>
                            <input type="url" class="form-control" id="add_site" name="site" placeholder="https://">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label" for="add_segmento_id">Segmento</label>
                            <select class="form-select" id="add_segmento_id" name="segmento_id">
                                <option value="">Selecione um segmento...</option>
                                @foreach(App\Models\Segmento::orderBy('nome')->get() as $segmento)
                                    <option value="{{ $segmento->id }}">{{ $segmento->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Salvar</button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Edição -->
    <div class="modal fade" id="modalEditCliente" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-dialog-centered">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Editar Cliente</h3>
                        <p class="text-muted">Altere os dados do cliente</p>
                    </div>

                    <form id="formEditCliente" class="row g-3" method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_atividades_secundarias_json" name="atividades_secundarias">

                        <!-- Dados Básicos do Cliente -->
                        <h5 class="mb-3 border-bottom pb-2">Dados Básicos</h5>

                        <div class="col-md-6">
                            <label class="form-label" for="edit_razao_social">Razão Social</label>
                            <input type="text" class="form-control" id="edit_razao_social" name="razao_social" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_cnpj">CNPJ</label>
                            <input type="text" class="form-control" id="edit_cnpj" name="cnpj" required>
                        </div>

                        <!-- Atividade Principal -->
                        <div class="col-md-12">
                            <label class="form-label" for="edit_atividade_principal">Atividade Principal</label>
                            <input type="text" class="form-control" id="edit_atividade_principal" name="atividade_principal" readonly>
                        </div>

                        <!-- Atividades Secundárias -->
                        <div class="col-md-12">
                            <label class="form-label">Atividades Secundárias</label>
                            <div id="edit_atividades_secundarias" class="border p-2 rounded bg-light" style="min-height: 40px; max-height: 120px; overflow-y: auto;">
                                <p class="text-muted mb-0 small">As atividades secundárias serão exibidas aqui.</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="edit_ie">Inscrição Estadual</label>
                            <input type="text" class="form-control" id="edit_ie" name="inscricao_estadual">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_suframa">SUFRAMA</label>
                            <input type="text" class="form-control" id="edit_suframa" name="suframa">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_email">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email">
                        </div>

                        <!-- Endereço -->
                        <h5 class="col-12 mt-3 mb-3 border-bottom pb-2">Endereço</h5>

                        <div class="col-md-3">
                            <label class="form-label" for="edit_cep">CEP</label>
                            <input type="text" class="form-control" id="edit_cep" name="cep">
                        </div>
                        <div class="col-md-9">
                            <label class="form-label" for="edit_endereco">Endereço</label>
                            <input type="text" class="form-control" id="edit_endereco" name="endereco" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="edit_codigo_ibge">Código IBGE</label>
                            <input type="text" class="form-control" id="edit_codigo_ibge" name="codigo_ibge" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="edit_municipio">Município</label>
                            <input type="text" class="form-control" id="edit_municipio" name="municipio" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="edit_uf">UF</label>
                            <input type="text" class="form-control" id="edit_uf" name="uf" maxlength="2" required>
                        </div>

                        <!-- Contato -->
                        <h5 class="col-12 mt-3 mb-3 border-bottom pb-2">Contato</h5>

                        <div class="col-md-6">
                            <label class="form-label" for="edit_telefone">Telefone</label>
                            <input type="text" class="form-control" id="edit_telefone" name="telefone" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_telefone2">Telefone 2</label>
                            <input type="text" class="form-control" id="edit_telefone2" name="telefone2">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_contato">Contato</label>
                            <input type="text" class="form-control" id="edit_contato" name="contato" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_site">Site</label>
                            <input type="url" class="form-control" id="edit_site" name="site" placeholder="https://">
                        </div>

                        <!-- Seção para Segmento -->
                        <h5 class="col-12 mt-3 mb-3 border-bottom pb-2">Negócios</h5>

                        <div class="col-md-12">
                            <label class="form-label" for="edit_segmento_id">Segmento</label>
                            <select class="form-select" id="edit_segmento_id" name="segmento_id">
                                <option value="">Selecione um segmento...</option>
                                @foreach(App\Models\Segmento::orderBy('nome')->get() as $segmento)
                                    <option value="{{ $segmento->id }}">{{ $segmento->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Dados da API CNPJa -->
                        <div class="col-12 mt-4">
                            <div class="accordion" id="accordionDadosCNPJa">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseApiData" aria-expanded="false" aria-controls="collapseApiData">
                                            Dados Complementares (API CNPJa)
                                        </button>
                                    </h2>
                                    <div id="collapseApiData" class="accordion-collapse collapse" data-bs-parent="#accordionDadosCNPJa">
                                        <div class="accordion-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">Nome Fantasia</label>
                                                    <input type="text" class="form-control" id="edit_nome_fantasia" name="nome_fantasia" readonly>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Fundação</label>
                                                    <input type="text" class="form-control" id="edit_fundacao" name="fundacao" readonly>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Situação</label>
                                                    <input type="text" class="form-control" id="edit_situacao" name="situacao" readonly>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Data Situação</label>
                                                    <input type="text" class="form-control" id="edit_data_situacao" name="data_situacao" readonly>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Natureza Jurídica</label>
                                                    <input type="text" class="form-control" id="edit_natureza_juridica" name="natureza_juridica" readonly>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Porte</label>
                                                    <input type="text" class="form-control" id="edit_porte" name="porte" readonly>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Capital Social</label>
                                                    <input type="text" class="form-control" id="edit_capital_social" name="capital_social" readonly>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Simples Nacional</label>
                                                    <input type="text" class="form-control" id="edit_simples_nacional" name="simples_nacional" readonly>
                                                </div>

                                                <!-- Sócios -->
                                                <div class="col-12 mt-3">
                                                    <h6 class="border-bottom pb-2">Sócios</h6>
                                                    <div id="edit_socios_container" class="border p-2 rounded bg-light" style="min-height: 40px; max-height: 150px; overflow-y: auto;">
                                                        <p class="text-muted mb-0 small">Informações sobre sócios serão exibidas aqui.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 text-center mt-4">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Atualizar</button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Outros modais -->
    @include('components.form-cliente')
    @include('components.form-transportadora')
    @include('components.form-veiculo')

    <!-- Modal de Edição de Transportadora -->
    <div class="modal fade" id="modalEditTransportadora" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-dialog-centered">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Editar Transportadora</h3>
                        <p class="text-muted">Altere os dados da transportadora</p>
                    </div>

                    <form id="formEditTransportadora" class="row g-3" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="col-md-6">
                            <label class="form-label" for="edit_trans_razao_social">Razão Social</label>
                            <input type="text" class="form-control" id="edit_trans_razao_social" name="razao_social" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_trans_cnpj">CNPJ</label>
                            <input type="text" class="form-control" id="edit_trans_cnpj" name="cnpj" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_trans_ie">Inscrição Estadual</label>
                            <input type="text" class="form-control" id="edit_trans_ie" name="inscricao_estadual">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_trans_endereco">Endereço</label>
                            <input type="text" class="form-control" id="edit_trans_endereco" name="endereco" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_trans_codigo_ibge">Código IBGE</label>
                            <input type="text" class="form-control" id="edit_trans_codigo_ibge" name="codigo_ibge" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_trans_telefone">Telefone</label>
                            <input type="text" class="form-control" id="edit_trans_telefone" name="telefone" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_trans_email">Email</label>
                            <input type="email" class="form-control" id="edit_trans_email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_trans_contato">Contato</label>
                            <input type="text" class="form-control" id="edit_trans_contato" name="contato" required>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Atualizar</button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Edição de Veículo -->
    <div class="modal fade" id="modalEditVeiculo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-simple modal-dialog-centered">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="mb-2">Editar Veículo</h3>
                        <p class="text-muted">Altere os dados do veículo</p>
                    </div>

                    <form id="formEditVeiculo" class="row g-3" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="col-md-6">
                            <label class="form-label" for="edit_veiculo_motorista">Motorista</label>
                            <input type="text" class="form-control" id="edit_veiculo_motorista" name="motorista" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_veiculo_placa">Placa</label>
                            <input type="text" class="form-control" id="edit_veiculo_placa" name="placa" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="edit_veiculo_marca">Marca</label>
                            <input type="text" class="form-control" id="edit_veiculo_marca" name="marca" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="edit_veiculo_modelo">Modelo</label>
                            <input type="text" class="form-control" id="edit_veiculo_modelo" name="modelo" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="edit_veiculo_ano">Ano</label>
                            <input type="number" class="form-control" id="edit_veiculo_ano" name="ano_fabricacao" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_veiculo_tipo_rodagem">Tipo de Rodagem</label>
                            <select class="form-select" id="edit_veiculo_tipo_rodagem" name="tipo_rodagem" required>
                                <option value="truck">Truck</option>
                                <option value="toco">Toco</option>
                                <option value="cavalo_mecanico">Cavalo Mecânico</option>
                                <option value="van">Van</option>
                                <option value="utilitarios">Utilitários</option>
                                <option value="outros">Outros</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_veiculo_tipo_carroceria">Tipo de Carroceria</label>
                            <select class="form-select" id="edit_veiculo_tipo_carroceria" name="tipo_carroceria" required>
                                <option value="aberta">Aberta</option>
                                <option value="bau">Baú</option>
                                <option value="slider">Sider</option>
                                <option value="outros">Outros</option>
                            </select>
                        </div>
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-primary me-sm-3 me-1">Atualizar</button>
                            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Adicionar Segmento -->
    <div class="modal fade" id="modalAddSegmento" tabindex="-1" aria-labelledby="modalAddSegmentoLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('segmentos.store') }}">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAddSegmentoLabel">Adicionar Segmento</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome do Segmento</label>
                            <input type="text" class="form-control" name="nome" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('components.modal-atendimento')
@endpush

@push('styles')
<style>
/* Estilos para garantir que o modal fique sobre tudo */
.modal {
    z-index: 1090 !important; /* Maior que o z-index do layout do Sneat */
}

.modal-backdrop {
    z-index: 1089 !important; /* Um nível abaixo do modal */
}

/* Evitar que o modal fique preso */
.modal-dialog {
    margin: 0.5rem auto;
    max-width: 95%;
    width: 1140px; /* tamanho máximo para modal-xl */
}

/* Garantir que o conteúdo do modal não seja cortado */
.modal-content {
    max-height: calc(100vh - 2rem);
    overflow-y: auto;
}

/* Estilo específico para o tema Sneat */
.modal-simple .modal-content {
    box-shadow: 0 0.25rem 1rem rgba(161, 172, 184, 0.45);
}

/* Corrigir posicionamento do botão de fechar */
.modal .btn-close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    z-index: 2;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Função genérica para carregar dados em um modal
    function setupModalEdit(modalId, route) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const form = this.querySelector('form');

                form.action = route.replace('__id__', id);

                fetch(`${route.replace('__id__', id)}/edit`)
                    .then(response => {
                        if (!response.ok) throw new Error('Erro na resposta da rede');
                        return response.json();
                    })
                    .then(data => {
                        if (!data) throw new Error('Dados não encontrados');

                        // Preenche todos os campos do formulário
                        Object.keys(data).forEach(key => {
                            const input = form.querySelector(`[name="${key}"]`);
                            if (input) input.value = data[key] || '';
                        });
                    })
                    .catch(error => {
                        console.error('Erro ao carregar dados:', error);
                        alert('Erro ao carregar dados');
                    });
            });
        }
    }

    // Inicializa os modais
    setupModalEdit('modalEditTransportadora', '/customers/transportadoras/__id__');
    setupModalEdit('modalEditVeiculo', '/customers/veiculos/__id__');
});
</script>
@endpush
