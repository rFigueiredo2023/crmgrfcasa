@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/horizontalLayout')

@section('title', 'Atendimentos')

@section('content')
    <div class="container mt-4">
        <h4 class="py-3 mb-4">
            <span class="text-muted fw-light">Cadastros /</span> Atendimentos
        </h4>

        <!-- Pills Nav Tabs -->
        <div class="nav-align-top mb-4">
            <ul class="nav nav-pills mb-4 nav-fill" role="tablist">
                <li class="nav-item mb-1 mb-sm-0">
                    <button type="button" class="nav-link active" role="tab" data-bs-toggle="tab" data-bs-target="#aba-cliente" aria-controls="aba-cliente" aria-selected="true">
                        <i class="bx bx-user icon-sm me-1"></i>Cliente
                    </button>
                </li>
                <li class="nav-item mb-1 mb-sm-0">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#aba-leads" aria-controls="aba-leads" aria-selected="false">
                        <i class="bx bx-bulb icon-sm me-1"></i>Leads
                    </button>
                </li>
                <li class="nav-item">
                    <button type="button" class="nav-link" role="tab" data-bs-toggle="tab" data-bs-target="#aba-novo-lead" aria-controls="aba-novo-lead" aria-selected="false">
                        <i class="bx bx-plus icon-sm me-1"></i>Novo Lead
                    </button>
                </li>
            </ul>

            <div class="tab-content">
                <!-- ABA CLIENTE -->
                <div class="tab-pane fade show active" id="aba-cliente" role="tabpanel">
                    @include('atendimentos.tabs.cliente')
                </div>

                <!-- ABA LEADS -->
                <div class="tab-pane fade" id="aba-leads" role="tabpanel">
                    @include('atendimentos.tabs.leads')
                </div>

                <!-- ABA NOVO LEAD -->
                <div class="tab-pane fade" id="aba-novo-lead" role="tabpanel">
                    @include('atendimentos.tabs.novo-lead')
                </div>
            </div>
        </div>
        <!-- /Pills Nav Tabs -->
    </div>
@endsection

@section('page-script')
    <script>
        function confirmDelete(id) {
            if (confirm('Tem certeza que deseja excluir este atendimento?')) {
                // implementar a lógica aqui
            }
        }
    </script>
@endsection
