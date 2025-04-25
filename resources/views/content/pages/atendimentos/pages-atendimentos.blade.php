{{-- View de Pages atendimentos relacionada a content/pages/atendimentos --}}
@extends('layouts/horizontalLayout')

@section('title', 'Atendimentos')

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss',
        'resources/assets/vendor/libs/select2/select2.scss',
        'resources/assets/vendor/libs/@form-validation/form-validation.scss',
        'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
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
        'resources/assets/vendor/libs/cleavejs/cleave-phone.js',
        'resources/assets/vendor/libs/sweetalert2/sweetalert2.js'
    ])
@endsection

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Pills Nav -->
<div class="nav-align-top mb-4">
    <div class="card">
        <div class="card-body">
            <ul class="nav nav-pills mb-3 nav-fill" role="tablist">
                <li class="nav-item">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#navs-clientes" aria-controls="navs-clientes" aria-selected="true">
                        <i class="bx bx-user me-1"></i> Clientes
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#navs-leads" aria-controls="navs-leads" aria-selected="false">
                        <i class="bx bx-bulb me-1"></i> Leads
                    </button>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade show active" id="navs-clientes" role="tabpanel">
                    @include('atendimentos.tabs.cliente')
                </div>
                <div class="tab-pane fade" id="navs-leads" role="tabpanel">
                    @include('atendimentos.tabs.leads')
                </div>
            </div>
        </div>
    </div>
</div>
<!-- / Pills Nav -->

@include('components.form-lead-atendimento')
@include('components.modal-atendimento')
@include('components.modal-historico-cliente')
@include('components.modal-historico-lead')

<!-- Modal de Atendimento (incluído diretamente) -->
<div class="modal fade" id="modalAtendimento" tabindex="-1" aria-labelledby="modalAtendimentoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAtendimentoLabel">Novo Atendimento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formAtendimento" action="{{ route('atendimentos.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="cliente_id" id="cliente_id">
                    <input type="hidden" name="data_atendimento" value="{{ now() }}">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Cliente</label>
                            <input type="text" class="form-control" id="cliente_nome" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" name="status" required>
                                <option value="">Selecione o status</option>
                                @foreach (App\Enums\StatusAtendimento::cases() as $status)
                                    <option value="{{ $status->value }}">{{ $status->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tipo de Contato</label>
                            <select class="form-select" name="tipo_contato" required>
                                <option value="">Selecione o tipo...</option>
                                <option value="Ligação">Ligação</option>
                                <option value="WhatsApp">WhatsApp</option>
                                <option value="E-mail">E-mail</option>
                                <option value="Visita">Visita</option>
                                <option value="Reunião">Reunião</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Descrição</label>
                            <textarea class="form-control" name="descricao" rows="4" required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Detalhes do Atendimento</label>
                            <textarea class="form-control" name="detalhes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-script')
    <script>
        // Diagnóstico e correção de layout
        document.addEventListener('DOMContentLoaded', function() {
            // Registrar altura inicial da página
            const monitorLayout = function() {
                console.log('Monitorando alterações de layout...');

                // Obter elementos principais do layout
                const content = document.querySelector('.container-fluid');
                const cardContent = document.querySelector('.card-body');

                if (!content || !cardContent) return;

                // Registrar dimensões iniciais
                const initialContentHeight = content.offsetHeight;
                const initialCardHeight = cardContent.offsetHeight;

                console.log('Dimensões iniciais:', {
                    contentHeight: initialContentHeight,
                    cardHeight: initialCardHeight,
                });

                // Usar MutationObserver para detectar mudanças no DOM
                const observer = new MutationObserver(function(mutations) {
                    // Verificar alterações nas dimensões
                    const currentContentHeight = content.offsetHeight;
                    const currentCardHeight = cardContent.offsetHeight;

                    if (currentContentHeight > initialContentHeight + 50) {
                        console.log('Alteração detectada no tamanho do conteúdo!', {
                            original: initialContentHeight,
                            atual: currentContentHeight,
                            diferença: currentContentHeight - initialContentHeight
                        });

                        // Tentar identificar o que causou a mudança
                        const tallElements = [];
                        document.querySelectorAll('body > *').forEach(el => {
                            if (el.offsetHeight > 200 && el.id !== 'app-container') {
                                tallElements.push({
                                    element: el,
                                    id: el.id,
                                    class: el.className,
                                    height: el.offsetHeight
                                });
                            }
                        });

                        console.log('Elementos grandes detectados:', tallElements);

                        // Identificar e remover elementos indesejados que podem estar causando o problema
                        // (adicionaremos código específico aqui quando identificarmos o elemento)
                    }
                });

                // Configurar o observer para monitorar todo o DOM
                observer.observe(document.body, {
                    childList: true,
                    subtree: true,
                    attributes: true,
                    characterData: false
                });

                // Monitorar após carregamento completo (incluindo imagens e outros recursos)
                window.addEventListener('load', function() {
                    console.log('Página totalmente carregada');

                    // Verificação programada para capturar alterações que acontecem após o carregamento
                    setTimeout(() => {
                        const currentContentHeight = content.offsetHeight;
                        const currentCardHeight = cardContent.offsetHeight;

                        console.log('Dimensões após carregamento:', {
                            contentHeight: currentContentHeight,
                            cardHeight: currentCardHeight,
                            diferençaContent: currentContentHeight - initialContentHeight,
                            diferençaCard: currentCardHeight - initialCardHeight
                        });

                        // Remover espaçamento indesejado se detectado
                        if (currentContentHeight > initialContentHeight + 50) {
                            // Tentativa 1: Forçar altura fixa (apenas se necessário)
                            content.style.height = initialContentHeight + 'px';
                            content.style.minHeight = initialContentHeight + 'px';
                            content.style.maxHeight = initialContentHeight + 'px';
                            content.style.overflow = 'auto';

                            console.log('Aplicada correção de altura fixa');
                        }
                    }, 1500); // Verificar 1.5 segundos após o carregamento
                });
            };

            // Ativar monitoramento
            monitorLayout();

            // Script para inicializar o modal e gerenciar eventos
            const modalAtendimento = document.getElementById('modalAtendimento');
            if (modalAtendimento) {
                const formAtendimento = document.querySelector('#formAtendimento');

                // Eventos para o modal
                modalAtendimento.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;

                    if (button) {
                        // Captura os dados do botão
                        const id = button.getAttribute('data-cliente-id') || button.getAttribute('data-id');
                        const nome = button.getAttribute('data-cliente-nome') || button.getAttribute('data-nome');
                        const tipo = button.getAttribute('data-tipo') === 'lead' ? 'lead' : 'cliente';

                        // Preenche os dados no formulário
                        if (id && nome) {
                            document.getElementById('cliente_id').value = id;
                            document.getElementById('cliente_nome').value = nome;

                            // Armazena o tipo para uso no envio
                            formAtendimento.setAttribute('data-tipo', tipo);
                        }
                    }
                });

                // Trata o envio do formulário
                formAtendimento.addEventListener('submit', function(e) {
                    e.preventDefault();

                    const tipo = this.getAttribute('data-tipo') || 'cliente';
                    const id = document.getElementById('cliente_id').value;
                    let url = this.action;

                    // Ajusta a URL para leads se necessário
                    if (tipo === 'lead') {
                        url = `/leads/${id}/atendimento`;
                    }

                    // Envia o formulário via AJAX
                    const formData = new FormData(this);

                    fetch(url, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Fecha o modal
                            bootstrap.Modal.getInstance(modalAtendimento).hide();

                            // Mostra mensagem de sucesso
                            Swal.fire({
                                icon: 'success',
                                title: 'Sucesso!',
                                text: data.message || 'Atendimento registrado com sucesso!',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => {
                                // Recarrega a página
                                window.location.reload();
                            });
                        } else {
                            throw new Error(data.message || 'Erro ao salvar atendimento');
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);

                        Swal.fire({
                            icon: 'error',
                            title: 'Erro!',
                            text: error.message || 'Ocorreu um erro ao registrar o atendimento',
                            confirmButtonText: 'Ok'
                        });
                    });
                });
            }
        });
    </script>
@endsection
