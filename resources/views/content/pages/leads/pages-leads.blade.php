@extends('layouts/contentNavbarLayout')

@section('title', 'Leads')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lista de Leads</h5>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalLead">
                    <i class="bx bx-plus me-1"></i> Novo Lead
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive text-nowrap">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Telefone</th>
                                <th>Email</th>
                                <th>Origem</th>
                                <th>Status</th>
                                <th>Cadastrado por</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @foreach($leads as $lead)
                            <tr>
                                <td>{{ $lead->nome }}</td>
                                <td>{{ $lead->telefone }}</td>
                                <td>{{ $lead->email }}</td>
                                <td>{{ $lead->origem }}</td>
                                <td>
                                    <span class="badge bg-{{ $lead->status == 'Quente' ? 'danger' : ($lead->status == 'Morno' ? 'warning' : 'info') }}">
                                        {{ $lead->status }}
                                    </span>
                                </td>
                                <td>{{ $lead->usuario->name }}</td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalLeadEdit{{ $lead->id }}">
                                                <i class="bx bx-edit-alt me-1"></i> Editar
                                            </a>
                                            <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item" onclick="return confirm('Tem certeza que deseja excluir este lead?')">
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
</div>

<!-- Modal para criar novo lead -->
<div class="modal fade" id="modalLead" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLeadTitle">Novo Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('leads.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="nome" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome" name="nome" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="telefone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone" name="telefone" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="origem" class="form-label">Origem</label>
                            <input type="text" class="form-control" id="origem" name="origem" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="Frio">Frio</option>
                                <option value="Morno">Morno</option>
                                <option value="Quente">Quente</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="observacoes" class="form-label">Observações</label>
                            <textarea class="form-control" id="observacoes" name="observacoes" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modais para editar leads -->
@foreach($leads as $lead)
<div class="modal fade" id="modalLeadEdit{{ $lead->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLeadEditTitle{{ $lead->id }}">Editar Lead</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('leads.update', $lead) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="nome{{ $lead->id }}" class="form-label">Nome</label>
                            <input type="text" class="form-control" id="nome{{ $lead->id }}" name="nome" value="{{ $lead->nome }}" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="telefone{{ $lead->id }}" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="telefone{{ $lead->id }}" name="telefone" value="{{ $lead->telefone }}" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="email{{ $lead->id }}" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email{{ $lead->id }}" name="email" value="{{ $lead->email }}" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="origem{{ $lead->id }}" class="form-label">Origem</label>
                            <input type="text" class="form-control" id="origem{{ $lead->id }}" name="origem" value="{{ $lead->origem }}" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="status{{ $lead->id }}" class="form-label">Status</label>
                            <select class="form-select" id="status{{ $lead->id }}" name="status" required>
                                <option value="Frio" {{ $lead->status == 'Frio' ? 'selected' : '' }}>Frio</option>
                                <option value="Morno" {{ $lead->status == 'Morno' ? 'selected' : '' }}>Morno</option>
                                <option value="Quente" {{ $lead->status == 'Quente' ? 'selected' : '' }}>Quente</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label for="observacoes{{ $lead->id }}" class="form-label">Observações</label>
                            <textarea class="form-control" id="observacoes{{ $lead->id }}" name="observacoes" rows="3">{{ $lead->observacoes }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection 