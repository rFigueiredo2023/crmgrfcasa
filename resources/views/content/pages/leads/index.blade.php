{{-- Página principal de content/pages/leads --}}
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>RAZÃO SOCIAL</th>
                <th>CNPJ</th>
                <th>TELEFONE</th>
                <th>CONTATO</th>
                <th>ÚLTIMO ATENDIMENTO</th>
                <th>VENDEDORA RESPONSÁVEL</th>
                <th>AÇÕES</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leads as $lead)
            <tr>
                <td>{{ $lead->nome_empresa }}</td>
                <td>{{ $lead->cnpj }}</td>
                <td>{{ $lead->telefone }}</td>
                <td>{{ $lead->contato }}</td>
                <td>
                    @if($lead->ultimoAtendimento)
                        {{ $lead->ultimoAtendimento->data->format('d/m/Y H:i') }}
                        <br>
                        <small class="text-muted">{{ $lead->ultimoAtendimento->descricao }}</small>
                    @else
                        -
                    @endif
                </td>
                <td>{{ $lead->user->name }}</td>
                <td>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-primary" onclick="abrirModalHistorico({{ $lead->id }})">
                            <i class="ti ti-history"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-info" onclick="abrirModalAtendimento({{ $lead->id }})">
                            <i class="ti ti-phone"></i>
                        </button>
                        <a href="{{ route('leads.edit', $lead->id) }}" class="btn btn-sm btn-warning">
                            <i class="ti ti-edit"></i>
                        </a>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div> 