<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Segmentos</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAddSegmento">
            <i class="bx bx-plus me-0 me-sm-1"></i>
            <span class="d-none d-sm-inline-block">Adicionar Segmento</span>
        </button>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="tabela-segmentos">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Clientes</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(App\Models\Segmento::withCount('clientes')->orderBy('nome')->get() as $segmento)
                    <tr>
                        <td>{{ $segmento->id }}</td>
                        <td>{{ $segmento->nome }}</td>
                        <td>{{ $segmento->clientes_count }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalEditSegmento" data-id="{{ $segmento->id }}">
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

<!-- Modal Adicionar Segmento -->
<div class="modal fade" id="modalAddSegmento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Adicionar Segmento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('segmentos.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <label for="nome" class="form-label">Nome do Segmento</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Segmento -->
<div class="modal fade" id="modalEditSegmento" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Editar Segmento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditSegmento" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <label for="edit_nome" class="form-label">Nome do Segmento</label>
                            <input type="text" class="form-control" id="edit_nome" name="nome" required>
                        </div>
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
        // Inicializar a tabela com DataTables
        $('#tabela-segmentos').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/pt-BR.json'
            }
        });

        // Modal de edição
        $('#modalEditSegmento').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const segmentoId = button.data('id');
            const form = document.getElementById('formEditSegmento');

            // Configurar a URL do formulário
            form.action = `/customers/segmentos/${segmentoId}`;

            // Buscar dados do segmento
            fetch(`/customers/segmentos/${segmentoId}/edit`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('edit_nome').value = data.nome;
                })
                .catch(error => {
                    console.error('Erro ao carregar segmento:', error);
                    alert('Erro ao carregar dados do segmento');
                });
        });
    });
</script>
