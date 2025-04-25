{{-- Página principal de content/pages/segmentos --}}
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
        <div class="col-md-8">
            <!-- Tabela de Segmentos -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Segmentos Cadastrados</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped" id="tabela-segmentos">
                            <thead>
                                <tr>
                                    <th width="10%">ID</th>
                                    <th width="70%">Nome</th>
                                    <th width="10%">Clientes</th>
                                    <th width="10%">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($segmentos as $segmento)
                                <tr>
                                    <td>{{ $segmento->id }}</td>
                                    <td>{{ $segmento->nome }}</td>
                                    <td>{{ $segmento->clientes()->count() }}</td>
                                    <td>
                                        <div class="dropdown">
                                            <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i class="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div class="dropdown-menu">
                                                <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalEditSegmento"
                                                    data-id="{{ $segmento->id }}" data-nome="{{ $segmento->nome }}">
                                                    <i class="bx bx-edit-alt me-1"></i> Editar
                                                </button>
                                                <form action="{{ route('segmentos.destroy', $segmento->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item" onclick="return confirm('Tem certeza que deseja excluir este segmento?')">
                                                        <i class="bx bx-trash me-1"></i> Excluir
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <!-- Formulário de Cadastro -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Adicionar Segmento</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('segmentos.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label for="nome" class="form-label">Nome do Segmento</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Salvar</button>
                    </form>
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
<!-- Modal Editar Segmento -->
<div class="modal fade" id="modalEditSegmento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="formEditSegmento" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Editar Segmento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nome" class="form-label">Nome do Segmento</label>
                        <input type="text" class="form-control" id="edit_nome" name="nome" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal de edição
        $('#modalEditSegmento').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const segmentoId = button.data('id');
            const segmentoNome = button.data('nome');
            const form = document.getElementById('formEditSegmento');

            // Configurar a URL do formulário
            form.action = `/customers/segmentos/${segmentoId}`;

            // Preencher o campo de nome
            document.getElementById('edit_nome').value = segmentoNome;
        });
    });
</script>
@endpush
