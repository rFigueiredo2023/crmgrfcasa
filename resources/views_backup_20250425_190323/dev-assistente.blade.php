{{-- View sem descrição detalhada. Revisar manualmente. --}}
@extends('layouts.contentNavbarLayout')

@section('title', 'Assistente de Desenvolvimento')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
  <h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Dev /</span> Assistente
  </h4>

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">
            <i class="bx bx-brain me-2"></i>Assistente de Desenvolvimento
          </h5>
          <small class="text-muted float-end">Diagnóstico automático</small>
        </div>
        <div class="card-body">
          @if ($errors->any())
            <div class="alert alert-danger mb-3">
              <ul class="mb-0">
                @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <form method="POST" action="{{ route('dev-assistente.perguntar') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label" for="prompt">Descreva o problema ou faça uma pergunta:</label>
              <textarea id="prompt" name="prompt" class="form-control" rows="5" placeholder="Descreva o problema ou erro que você está enfrentando...">{{ old('prompt', $prompt ?? '') }}</textarea>
            </div>
            <div class="d-flex justify-content-between">
              <a href="{{ route('dev-assistente') }}" class="btn btn-outline-secondary">
                <i class="bx bx-reset me-1"></i>Limpar
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="bx bx-send me-1"></i>Perguntar
              </button>
            </div>
          </form>

          @if(isset($resposta))
            <div class="mt-4">
              <div class="divider">
                <div class="divider-text">Resposta do Assistente</div>
              </div>
              <div class="p-3 bg-light rounded markdown-content">
                {!! nl2br(e($resposta)) !!}
              </div>
            </div>
          @endif
        </div>
      </div>

      @if(isset($resposta))
      <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">
            <i class="bx bx-info-circle me-2"></i>Diagnóstico do Problema
          </h5>
        </div>
        <div class="card-body">
          <p class="text-muted">
            O assistente analisa o erro apresentado e tenta oferecer uma solução prática.
            Se precisar de mais informações ou detalhes, faça perguntas específicas.
          </p>
          <div class="mt-3">
            <a href="{{ route('dev-assistente') }}" class="btn btn-sm btn-outline-primary">
              <i class="bx bx-plus me-1"></i>Nova Pergunta
            </a>
          </div>
        </div>
      </div>
      @endif
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
  // Ajusta o foco e scroll quando a página carrega
  document.addEventListener('DOMContentLoaded', function() {
    // Ajusta foco no campo de prompt
    document.getElementById('prompt').focus();

    // Se existir prompt e resposta, role até a resposta
    @if(isset($resposta) && isset($prompt))
      document.querySelector('.markdown-content').scrollIntoView({behavior: 'smooth'});
    @endif
  });
</script>
@endsection
