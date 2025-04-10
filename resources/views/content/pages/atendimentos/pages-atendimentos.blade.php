@extends('layouts/horizontalLayout')

@section('title', 'Atendimentos')

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
        </div>
    </div>
    <!-- /Pills Nav Tabs -->
@endsection
