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

@section('page-script')
    <script>
        function confirmDelete(id) {
            if (confirm('Tem certeza que deseja excluir este atendimento?')) {
                // implementar a lógica aqui
            }
        }
    </script>
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
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Atendimentos</h5>
        </div>
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
@include('components.modal-historico-cliente')
@endsection
