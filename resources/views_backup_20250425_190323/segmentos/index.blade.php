{{-- Página principal de segmentos --}}
@extends('layouts/horizontalLayout')

@section('title', 'Segmentos')

@section('vendor-style')
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
        'resources/assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.scss'
    ])
@endsection

@section('vendor-script')
    @vite([
        'resources/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js'
    ])
@endsection

@section('content')
<h4 class="py-3 mb-4">
  <span class="text-muted fw-light">Cadastros /</span> Segmentos
</h4>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Segmentos</h5>
        <a href="{{ route('segmentos.create') }}" class="btn btn-primary">
          <i class="bx bx-plus me-0 me-sm-1"></i>
          <span class="d-none d-sm-inline-block">Novo Segmento</span>
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

        <div class="table-responsive">
          <table class="table table-bordered">
            <thead>
              <tr>
                <th style="width: 80%">Nome</th>
                <th class="text-center">Ações</th>
              </tr>
            </thead>
            <tbody>
              @forelse($segmentos as $segmento)
                <tr>
                  <td>{{ $segmento->nome }}</td>
                  <td class="text-center">
                    <div class="d-inline-block">
                      <a href="{{ route('segmentos.edit', $segmento->id) }}" class="btn btn-sm btn-icon btn-primary">
                        <i class="bx bx-edit"></i>
                      </a>
                      <form action="{{ route('segmentos.destroy', $segmento->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-icon btn-danger" onclick="return confirm('Tem certeza que deseja excluir este segmento?')">
                          <i class="bx bx-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="2" class="text-center">Nenhum segmento cadastrado.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
          {{ $segmentos->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Inicializar DataTables
    $('#tabela-segmentos').DataTable({
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
      }
    });
  });
</script>
@endsection

@push('modals')
<!-- Modal Adicionar Segmento -->
<div class="modal fade" id="modalSegmento" tabindex="-1" aria-labelledby="modalSegmentoLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form method="POST" action="{{ route('segmentos.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalSegmentoLabel">Adicionar Segmento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="nome" class="form-label">Nome do Segmento</label>
            <input type="text" name="nome" class="form-control" required>
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
@endpush
