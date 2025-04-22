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
            </div>
        </div>
    </div>
@endsection

@section('page-script')
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
                        <div class="col-md-6">
                            <label class="form-label" for="add_razao_social">Razão Social</label>
                            <input type="text" class="form-control" id="add_razao_social" name="razao_social" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="add_cnpj">CNPJ</label>
                            <input type="text" class="form-control" id="add_cnpj" name="cnpj" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="add_ie">Inscrição Estadual</label>
                            <input type="text" class="form-control" id="add_ie" name="inscricao_estadual">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="add_endereco">Endereço</label>
                            <input type="text" class="form-control" id="add_endereco" name="endereco" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="add_codigo_ibge">Código IBGE</label>
                            <input type="text" class="form-control" id="add_codigo_ibge" name="codigo_ibge" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="add_telefone">Telefone</label>
                            <input type="text" class="form-control" id="add_telefone" name="telefone" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label" for="add_contato">Contato</label>
                            <input type="text" class="form-control" id="add_contato" name="contato" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label" for="add_segmento">Segmento</label>
                            <input type="text" class="form-control" id="add_segmento" name="segmento">
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
                        <div class="col-md-6">
                            <label class="form-label" for="edit_razao_social">Razão Social</label>
                            <input type="text" class="form-control" id="edit_razao_social" name="razao_social" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_cnpj">CNPJ</label>
                            <input type="text" class="form-control" id="edit_cnpj" name="cnpj" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_ie">Inscrição Estadual</label>
                            <input type="text" class="form-control" id="edit_ie" name="inscricao_estadual">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_endereco">Endereço</label>
                            <input type="text" class="form-control" id="edit_endereco" name="endereco" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_codigo_ibge">Código IBGE</label>
                            <input type="text" class="form-control" id="edit_codigo_ibge" name="codigo_ibge" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="edit_telefone">Telefone</label>
                            <input type="text" class="form-control" id="edit_telefone" name="telefone" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label" for="edit_contato">Contato</label>
                            <input type="text" class="form-control" id="edit_contato" name="contato" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label" for="edit_segmento">Segmento</label>
                            <input type="text" class="form-control" id="edit_segmento" name="segmento">
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
    setupModalEdit('modalEditCliente', '/customers/clientes/__id__');
    setupModalEdit('modalEditTransportadora', '/customers/transportadoras/__id__');
    setupModalEdit('modalEditVeiculo', '/customers/veiculos/__id__');
});
</script>
@endpush
