@extends('layouts/contentNavbarLayout')

@section('title', 'Clientes')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Gerenciamento de Clientes</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#clienteModal">
            <i class="bx bx-plus me-1"></i> Novo Cliente
        </button>
    </div>
    <div class="card-body">
        <!-- ... existing code ... -->
    </div>
</div>

@include('components.form-cliente')
@endsection
