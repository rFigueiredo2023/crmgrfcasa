<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Leads</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovoLeadAtendimento">
            <i class="bx bx-plus me-1"></i> Novo Lead
        </button>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="busca-lead" placeholder="Buscar lead...">
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
                    @forelse($leads as $lead)
                    <tr>
                        <td>{{ $lead->razao_social }}</td>
                        <td>{{ $lead->cnpj }}</td>
                        <td>{{ $lead->telefone }}</td>
                        <td>{{ $lead->contato }}</td>
                        <td>
                            @if($lead->ultimoAtendimento)
                                {{ $lead->ultimoAtendimento->created_at->format('d/m/Y H:i') }}
                            @else
                                Nenhum atendimento
                            @endif
                        </td>
                        <td>{{ $lead->vendedor->name ?? 'Não atribuído' }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalAtendimento" data-lead-id="{{ $lead->id }}" data-lead-nome="{{ $lead->razao_social }}">
                                <i class="bx bx-plus"></i>
                            </button>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-toggle="modal" data-bs-target="#modalHistoricoCliente" data-lead-id="{{ $lead->id }}">
                                <i class="bx bx-history"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Nenhum lead encontrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Máscara para telefone
    document.querySelectorAll('.phone-mask').forEach(function(element) {
        element.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{4,5})(\d)/, '$1-$2');
                e.target.value = value;
            }
        });
    });

    // Máscara para CNPJ
    document.querySelectorAll('.cnpj-mask').forEach(function(element) {
        element.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 14) {
                value = value.replace(/(\d{2})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1/$2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
                e.target.value = value;
            }
        });
    });

    // Função para confirmar exclusão
    function confirmarExclusao(id) {
        if (confirm('Tem certeza que deseja excluir este lead?')) {
            // Implementar lógica de exclusão
        }
    }

    // Busca de leads
    document.getElementById('busca-lead').addEventListener('input', function(e) {
        const busca = e.target.value.toLowerCase();
        const linhas = document.querySelectorAll('tbody tr');

        linhas.forEach(function(linha) {
            const texto = linha.textContent.toLowerCase();
            linha.style.display = texto.includes(busca) ? '' : 'none';
        });
    });
</script>
@endpush

