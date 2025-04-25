{{-- View de Pages leads relacionada a content/pages/leads --}}
@extends('layouts/contentNavbarLayout')

@section('title', 'Leads')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
@endsection

@section('page-script')
<script>
$(document).ready(function() {
    const dt_leads = $('.datatables-leads');
    
    if (dt_leads.length) {
        dt_leads.DataTable({
            dom: '<"card-header flex-column flex-md-row"<"head-label text-center"><"dt-action-buttons text-end pt-3 pt-md-0"B>><"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6 d-flex justify-content-center justify-content-md-end"f>>t<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            displayLength: 10,
            lengthMenu: [10, 25, 50, 75, 100],
            buttons: [
                {
                    extend: 'collection',
                    className: 'btn btn-label-primary dropdown-toggle me-2',
                    text: '<i class="bx bx-export me-sm-1"></i> <span class="d-none d-sm-inline-block">Exportar</span>',
                    buttons: [
                        {
                            extend: 'print',
                            text: '<i class="bx bx-printer me-1" ></i>Imprimir',
                            className: 'dropdown-item',
                            exportOptions: { columns: [1, 2, 3, 4, 5] }
                        },
                        {
                            extend: 'csv',
                            text: '<i class="bx bx-file me-1" ></i>CSV',
                            className: 'dropdown-item',
                            exportOptions: { columns: [1, 2, 3, 4, 5] }
                        },
                        {
                            extend: 'excel',
                            text: '<i class="bx bx-file me-1" ></i>Excel',
                            className: 'dropdown-item',
                            exportOptions: { columns: [1, 2, 3, 4, 5] }
                        },
                        {
                            extend: 'pdf',
                            text: '<i class="bx bx-file me-1" ></i>PDF',
                            className: 'dropdown-item',
                            exportOptions: { columns: [1, 2, 3, 4, 5] }
                        }
                    ]
                }
            ],
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json',
            }
        });
    }
});
</script>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-bottom">
                <h5 class="card-title mb-3">Lista de Leads</h5>
                <div class="d-flex justify-content-between align-items-center row pb-2 gap-3 gap-md-0">
                    <div class="col-md-4 user_role"></div>
                    <div class="col-md-4 user_plan"></div>
                    <div class="col-md-4 user_status"></div>
                </div>
            </div>
            <div class="card-datatable table-responsive">
                <table class="datatables-leads table border-top">
                    <thead>
                        <tr>
                            <th>Nome/Empresa</th>
                            <th>CNPJ</th>
                            <th>Telefone</th>
                            <th>Contato</th>
                            <th>Último Atendimento</th>
                            <th>Vendedora</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leads as $lead)
                        <tr>
                            <td>{{ $lead->nome_empresa }}</td>
                            <td>{{ $lead->cnpj ?? 'Não informado' }}</td>
                            <td>{{ $lead->telefone }}</td>
                            <td>{{ $lead->contato }}</td>
                            <td>{{ $lead->ultimo_atendimento ? $lead->ultimo_atendimento->format('d/m/Y H:i') : 'Sem atendimentos' }}</td>
                            <td>{{ $lead->vendedor->name ?? 'Não atribuído' }}</td>
                            <td>
                                <div class="d-inline-block">
                                    <button class="btn btn-sm btn-icon btn-primary" title="Novo Atendimento">
                                        <i class="bx bx-headphone"></i>
                                    </button>
                                    <button class="btn btn-sm btn-icon btn-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalHistoricoCliente"
                                            data-lead-id="{{ $lead->id }}"
                                            title="Histórico">
                                        <i class="bx bx-history"></i>
                                    </button>
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

@include('components.modal-historico-cliente')
@endsection 