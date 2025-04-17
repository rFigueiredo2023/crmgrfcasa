<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Clientes</h5>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="busca-cliente" placeholder="Buscar cliente...">
                    <button class="btn btn-outline-primary" type="button">
                        <i class="bx bx-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>RAZÃO SOCIAL</th>
                        <th>CNPJ</th>
                        <th>TELEFONE</th>
                        <th>CONTATO</th>
                        <th>ÚLTIMO ATENDIMENTO</th>
                        <th>VENDEDORA RESPONSÁVEL</th>
                        <th>ATENDIMENTO</th>
                        <th>HISTÓRICO</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->razao_social }}</td>
                        <td>{{ $cliente->cnpj }}</td>
                        <td>{{ $cliente->telefone }}</td>
                        <td>{{ $cliente->contato }}</td>
                        <td>
                            @if($cliente->ultimoAtendimento)
                                {{ $cliente->ultimoAtendimento->created_at->format('d/m/Y H:i') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $cliente->vendedor->name ?? 'Não atribuído' }}</td>
                        <td>
                            <button type="button" class="btn btn-info btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalAtendimento"
                                    data-cliente-id="{{ $cliente->id }}"
                                    data-cliente-nome="{{ $cliente->razao_social }}">
                                <i class="bx bx-headphone me-1"></i>Atendimento
                            </button>
                        </td>
                        <td>
                            <button type="button"
                                    class="btn btn-icon btn-secondary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalHistorico"
                                    data-cliente-id="{{ $cliente->id }}"
                                    data-cliente-nome="{{ $cliente->razao_social }}"
                                    title="Ver histórico completo">
                                <i class="bx bx-time"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Nenhum cliente encontrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@include('components.modal-historico-cliente')

@push('scripts')
<script>
    // Busca de clientes
    const buscaInput = document.getElementById('busca-cliente');
    buscaInput.addEventListener('input', function(e) {
        const busca = e.target.value.toLowerCase();
        const linhas = document.querySelectorAll('tbody tr');

        linhas.forEach(function(linha) {
            const texto = linha.textContent.toLowerCase();
            linha.style.display = texto.includes(busca) ? '' : 'none';
        });
    });

    // Inicializa tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
</script>
@endpush
