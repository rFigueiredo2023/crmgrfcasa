{{-- Página de visualização de content/pages/atendimentos --}}
@extends('layouts/horizontalLayout')

@section('title', 'Detalhes do Atendimento')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Detalhes do Atendimento</h5>
                    <div>
                        <a href="{{ route('atendimentos.index') }}" class="btn btn-secondary">
                            <i class="bx bx-arrow-back me-1"></i> Voltar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Cliente</label>
                                <p class="form-control-static">{{ $atendimento->cliente->razao_social }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Vendedor</label>
                                <p class="form-control-static">{{ $atendimento->vendedor->name }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 