{{-- Página de edição de segmentos --}}
@extends('layouts/horizontalLayout')

@section('title', 'Editar Segmento')

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Cadastros / <a href="{{ route('segmentos.index') }}">Segmentos</a> /</span> Editar
</h4>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Editar Segmento</h5>
        <a href="{{ route('segmentos.index') }}" class="btn btn-secondary">
          <i class="bx bx-arrow-back me-0 me-sm-1"></i>
          <span class="d-none d-sm-inline-block">Voltar</span>
        </a>
      </div>
      <div class="card-body">
        @if(session('success'))
          <div class="alert alert-success alert-dismissible mb-3">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        @if(session('error'))
          <div class="alert alert-danger alert-dismissible mb-3">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <form action="{{ route('segmentos.update', $segmento->id) }}" method="POST">
          @csrf
          @method('PUT')
          <div class="row mb-3">
            <div class="col-md-6">
              <label for="nome" class="form-label">Nome do Segmento</label>
              <input type="text" class="form-control @error('nome') is-invalid @enderror" id="nome" name="nome" value="{{ old('nome', $segmento->nome) }}" required>
              @error('nome')
                <div class="invalid-feedback">
                  {{ $message }}
                </div>
              @enderror
              <div class="form-text">O nome do segmento deve ser único.</div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
              <button type="submit" class="btn btn-primary">Atualizar</button>
              <a href="{{ route('segmentos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
