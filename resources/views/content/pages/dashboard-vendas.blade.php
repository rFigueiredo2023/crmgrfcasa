@extends('layouts/horizontalLayout')

@section('title', 'Dashboard de Vendas')

@section('vendor-style')
    <link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
@endsection

@section('vendor-script')
    <script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
<script>
    // Configuração do gráfico de histórico da meta
    let options = {
        series: [{
            name: 'Meta',
            data: [100000, 100000, 100000]
        }, {
            name: 'Realizado',
            data: [85000, 92000, 64000]
        }],
        chart: {
            height: 150,
            type: 'line',
            toolbar: {
                show: false
            },
            sparkline: {
                enabled: true
            }
        },
        colors: ['#696cff', '#03c3ec'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 2
        },
        grid: {
            show: false
        },
        xaxis: {
            categories: ['Fev', 'Mar', 'Abr'],
            labels: {
                show: true
            },
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            }
        },
        yaxis: {
            show: false
        },
        legend: {
            show: false
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return 'R$ ' + val.toLocaleString('pt-BR');
                }
            }
        }
    };

    // Configuração do gráfico de progresso (donut)
    let progressOptions = {
        series: [64],
        chart: {
            height: 250,
            type: 'radialBar',
        },
        plotOptions: {
            radialBar: {
                hollow: {
                    size: '70%',
                },
                dataLabels: {
                    show: true,
                    name: {
                        show: true,
                        fontSize: '16px',
                        fontFamily: undefined,
                        color: undefined,
                        offsetY: -10
                    },
                    value: {
                        show: true,
                        fontSize: '30px',
                        fontFamily: undefined,
                        color: undefined,
                        offsetY: 5,
                        formatter: function (val) {
                            return val + '%';
                        }
                    }
                }
            }
        },
        colors: ['#696cff'],
        labels: ['Progresso'],
    };

    // Renderiza os gráficos quando o documento estiver pronto
    document.addEventListener('DOMContentLoaded', function() {
        const chart = new ApexCharts(document.querySelector("#historico-meta"), options);
        chart.render();

        const progressChart = new ApexCharts(document.querySelector("#progresso-meta"), progressOptions);
        progressChart.render();
    });
</script>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <p class="text-muted mb-4">Bem-vindo ao seu painel de vendas, {{ auth()->user()->name }}</p>
    </div>
</div>

<div class="row">
    <!-- Card Vendas no Mês -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Vendas no Mês</h5>
                        <small class="text-muted">Abril 2024</small>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-primary">
                            <i class="bx bx-trending-up"></i>
                        </span>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-end mt-4">
                    <div>
                        <h4 class="mb-2">17 vendas</h4>
                        <h2 class="mb-0 text-primary">R$ 58.000,00</h2>
                    </div>
                    <div class="badge bg-label-success">
                        +12% <i class="bx bx-chevron-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Progresso da Meta -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="card-title mb-0">Progresso da Meta</h5>
                        <small class="text-muted">Meta Mensal</small>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="bx bx-target-lock"></i>
                        </span>
                    </div>
                </div>
                <div id="progresso-meta"></div>
                <div class="text-center mt-3">
                    <p class="mb-0">Meta: R$ 100.000,00</p>
                    <p class="mb-0">Atingido: R$ 64.000,00</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Novos Negócios -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Novos Negócios</h5>
                        <small class="text-muted">Clientes Novos</small>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-success">
                            <i class="bx bx-user-plus"></i>
                        </span>
                    </div>
                </div>
                <div class="mt-4">
                    <h2 class="mb-2 text-success">5</h2>
                    <p class="mb-0">vendas para novos clientes</p>
                    <div class="badge bg-label-success mt-2">
                        +3 <i class="bx bx-chevron-up"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Histórico da Meta -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="bx bx-line-chart text-primary me-2"></i>
                    <h5 class="card-title mb-0">Histórico</h5>
                </div>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="historicoDrop" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="historicoDrop">
                        <a class="dropdown-item" href="javascript:void(0);">Últimos 6 meses</a>
                        <a class="dropdown-item" href="javascript:void(0);">Último ano</a>
                        <a class="dropdown-item" href="javascript:void(0);">Exportar dados</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="historico-meta" style="min-height: 150px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Nova linha para os cards adicionais -->
<div class="row">
    <!-- Card Novos E-mails -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Novos E-mails</h5>
                        <small class="text-muted">E-mails não lidos</small>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-info">
                            <i class="bx bx-envelope"></i>
                        </span>
                    </div>
                </div>
                <div class="mt-4">
                    <h2 class="mb-2">3</h2>
                    <div class="d-flex align-items-center">
                        <div class="badge bg-label-info me-2">
                            <i class="bx bx-envelope"></i>
                        </div>
                        <small>2 aguardando resposta</small>
                    </div>
                    <div class="mt-3">
                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-primary">Ver E-mails</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Notificações -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">Notificações</h5>
                <div class="badge bg-danger rounded-pill">3 novas</div>
            </div>
            <div class="card-body">
                <ul class="p-0 m-0">
                    <li class="d-flex mb-3">
                        <div class="avatar avatar-sm flex-shrink-0 me-3">
                            <span class="avatar-initial rounded-circle bg-label-warning">
                                <i class="bx bx-time"></i>
                            </span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">Retorno Vencido</h6>
                                <small class="text-muted">Cliente XPTO - 2 dias atrás</small>
                            </div>
                        </div>
                    </li>
                    <li class="d-flex mb-3">
                        <div class="avatar avatar-sm flex-shrink-0 me-3">
                            <span class="avatar-initial rounded-circle bg-label-info">
                                <i class="bx bx-bell"></i>
                            </span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">Orçamento Expirando</h6>
                                <small class="text-muted">Cliente ABC - Expira hoje</small>
                            </div>
                        </div>
                    </li>
                    <li class="d-flex">
                        <div class="avatar avatar-sm flex-shrink-0 me-3">
                            <span class="avatar-initial rounded-circle bg-label-success">
                                <i class="bx bx-check"></i>
                            </span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">Follow-up Agendado</h6>
                                <small class="text-muted">Cliente DEF - Amanhã</small>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Card Atividades Recentes -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title mb-0">Atividades Recentes</h5>
            </div>
            <div class="card-body">
                <ul class="timeline timeline-center mt-3">
                    <li class="timeline-item">
                        <span class="timeline-indicator timeline-indicator-primary">
                            <i class="bx bx-file"></i>
                        </span>
                        <div class="timeline-event">
                            <div class="timeline-header">
                                <small class="text-primary">Hoje 14:30</small>
                            </div>
                            <h6 class="mb-0">Orçamento Criado</h6>
                            <p class="text-muted mb-0">Cliente XYZ - R$ 15.000,00</p>
                        </div>
                    </li>
                    <li class="timeline-item">
                        <span class="timeline-indicator timeline-indicator-success">
                            <i class="bx bx-phone"></i>
                        </span>
                        <div class="timeline-event">
                            <div class="timeline-header">
                                <small class="text-success">Hoje 11:20</small>
                            </div>
                            <h6 class="mb-0">Follow-up Realizado</h6>
                            <p class="text-muted mb-0">Cliente ABC - Retorno em 3 dias</p>
                        </div>
                    </li>
                    <li class="timeline-item">
                        <span class="timeline-indicator timeline-indicator-info">
                            <i class="bx bx-user"></i>
                        </span>
                        <div class="timeline-event">
                            <div class="timeline-header">
                                <small class="text-info">Ontem 16:45</small>
                            </div>
                            <h6 class="mb-0">Novo Lead Cadastrado</h6>
                            <p class="text-muted mb-0">Empresa LMN</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Nova linha para os cards adicionais -->
<div class="row">
    <!-- Card Últimos Pedidos -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">Últimos Pedidos</h5>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="pedidosDrop" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="pedidosDrop">
                        <a class="dropdown-item" href="javascript:void(0);">Ver todos</a>
                        <a class="dropdown-item" href="javascript:void(0);">Exportar</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <ul class="p-0 m-0">
                    <li class="d-flex mb-3 pb-1">
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">#PED-2024-0123</h6>
                                <small class="text-muted">Empresa ABC Ltda</small>
                            </div>
                            <div class="user-progress d-flex align-items-center gap-1">
                                <span class="badge bg-label-success">Aprovado</span>
                                <small class="text-muted">20/04</small>
                            </div>
                        </div>
                    </li>
                    <li class="d-flex mb-3 pb-1">
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">#PED-2024-0122</h6>
                                <small class="text-muted">XYZ Comércio</small>
                            </div>
                            <div class="user-progress d-flex align-items-center gap-1">
                                <span class="badge bg-label-warning">Pendente</span>
                                <small class="text-muted">19/04</small>
                            </div>
                        </div>
                    </li>
                    <li class="d-flex mb-3 pb-1">
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">#PED-2024-0121</h6>
                                <small class="text-muted">123 Indústria</small>
                            </div>
                            <div class="user-progress d-flex align-items-center gap-1">
                                <span class="badge bg-label-success">Aprovado</span>
                                <small class="text-muted">18/04</small>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Card Últimos Orçamentos -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="card-title m-0">Últimos Orçamentos</h5>
                <div class="dropdown">
                    <button class="btn p-0" type="button" id="orcamentosDrop" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="orcamentosDrop">
                        <a class="dropdown-item" href="javascript:void(0);">Ver todos</a>
                        <a class="dropdown-item" href="javascript:void(0);">Exportar</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <ul class="p-0 m-0">
                    <li class="d-flex mb-3 pb-1">
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">Empresa ABC Ltda</h6>
                                <small class="text-muted">R$ 15.000,00</small>
                            </div>
                            <div class="user-progress d-flex align-items-center gap-1">
                                <span class="badge bg-label-info">Enviado</span>
                                <small class="text-muted">20/04</small>
                            </div>
                        </div>
                    </li>
                    <li class="d-flex mb-3 pb-1">
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">XYZ Comércio</h6>
                                <small class="text-muted">R$ 8.500,00</small>
                            </div>
                            <div class="user-progress d-flex align-items-center gap-1">
                                <span class="badge bg-label-warning">Aguardando</span>
                                <small class="text-muted">19/04</small>
                            </div>
                        </div>
                    </li>
                    <li class="d-flex mb-3 pb-1">
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">123 Indústria</h6>
                                <small class="text-muted">R$ 22.000,00</small>
                            </div>
                            <div class="user-progress d-flex align-items-center gap-1">
                                <span class="badge bg-label-success">Aprovado</span>
                                <small class="text-muted">18/04</small>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Card Atendimentos em Aberto -->
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="card-title mb-0">
                        <h5 class="mb-0">Atendimentos em Aberto</h5>
                        <small class="text-muted">Aguardando conclusão</small>
                    </div>
                    <div class="avatar">
                        <span class="avatar-initial rounded bg-label-warning">
                            <i class="bx bx-support"></i>
                        </span>
                    </div>
                </div>
                <div class="mt-4">
                    <h2 class="mb-2">7</h2>
                    <div class="d-flex align-items-center">
                        <div class="badge bg-label-warning me-2">
                            <i class="bx bx-time"></i>
                        </div>
                        <small>3 com retorno hoje</small>
                    </div>
                    <div class="mt-3">
                        <a href="javascript:void(0)" class="btn btn-sm btn-outline-primary">Ver Todos</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 