{{-- View de Admin relacionada a dashboards --}}
@extends('layouts.horizontalLayout')

@section('title', 'Dashboard Admin')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
<script>
  // Configuração do gráfico de vendas
  var optionsVendas = {
    series: [{
      name: 'Vendas',
      data: [44, 55, 57, 56, 61, 58, 63, 60, 66]
    }],
    chart: {
      type: 'bar',
      height: 350
    },
    plotOptions: {
      bar: {
        horizontal: false,
        columnWidth: '55%',
        endingShape: 'rounded'
      },
    },
    dataLabels: {
      enabled: false
    },
    stroke: {
      show: true,
      width: 2,
      colors: ['transparent']
    },
    xaxis: {
      categories: ['Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out'],
    },
    yaxis: {
      title: {
        text: 'R$ (milhares)'
      }
    },
    fill: {
      opacity: 1
    },
    tooltip: {
      y: {
        formatter: function (val) {
          return "R$ " + val + " mil"
        }
      }
    }
  };

  // Renderiza o gráfico quando a página carregar
  document.addEventListener('DOMContentLoaded', function() {
    let chartVendas = new ApexCharts(document.querySelector("#chartVendas"), optionsVendas);
    chartVendas.render();
  });
</script>
@endsection

@section('content')
<h4>Dashboard do Administrador</h4>
<p class="mb-4">Visão geral do sistema e estatísticas.</p>

<!-- Aqui serão adicionados os componentes específicos para o admin -->

<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <div class="card">
      <div class="d-flex align-items-end row">
        <div class="col-sm-7">
          <div class="card-body">
            <h5 class="card-title text-primary">Bem-vindo, {{ auth()->user()->name }}! 🎉</h5>
            <p class="mb-4">Você tem <span class="fw-bold">5 novas notificações</span> hoje. Confira o resumo do sistema abaixo.</p>
          </div>
        </div>
        <div class="col-sm-5 text-center text-sm-left">
          <div class="card-body pb-0 px-0 px-md-4">
            <img src="{{asset('assets/img/illustrations/man-with-laptop-light.png')}}" height="140" alt="View Badge User">
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Cards de Estatísticas -->
  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Vendas Totais</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="card-title mb-0 me-2">R$ 459.000</h4>
            </div>
            <small>Último mês: <span class="text-success">+12%</span></small>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-primary rounded p-2">
              <i class="bx bx-trending-up bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Novos Clientes</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="card-title mb-0 me-2">38</h4>
            </div>
            <small>Último mês: <span class="text-success">+8%</span></small>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-success rounded p-2">
              <i class="bx bx-user-plus bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Pedidos Pendentes</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="card-title mb-0 me-2">12</h4>
            </div>
            <small>Último mês: <span class="text-danger">+2</span></small>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-warning rounded p-2">
              <i class="bx bx-time-five bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Receita Mensal</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="card-title mb-0 me-2">R$ 95.000</h4>
            </div>
            <small>Último mês: <span class="text-success">+15%</span></small>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-info rounded p-2">
              <i class="bx bx-dollar bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Gráfico de Vendas -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Vendas nos Últimos Meses</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="vendasOptions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="bx bx-dots-vertical-rounded"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="vendasOptions">
            <a class="dropdown-item" href="javascript:void(0);">Ver Detalhes</a>
            <a class="dropdown-item" href="javascript:void(0);">Atualizar</a>
            <a class="dropdown-item" href="javascript:void(0);">Baixar Relatório</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div id="chartVendas"></div>
      </div>
    </div>
  </div>

  <!-- Últimas Atividades -->
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h5 class="mb-0">Últimas Atividades</h5>
      </div>
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>Usuário</th>
              <th>Atividade</th>
              <th>Status</th>
              <th>Data</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>João Silva</td>
              <td>Cadastrou novo cliente</td>
              <td><span class="badge bg-label-success">Concluído</span></td>
              <td>2024-04-08 14:30</td>
            </tr>
            <tr>
              <td>Maria Santos</td>
              <td>Atualizou pedido #1234</td>
              <td><span class="badge bg-label-warning">Pendente</span></td>
              <td>2024-04-08 13:15</td>
            </tr>
            <tr>
              <td>Pedro Oliveira</td>
              <td>Finalizou venda #5678</td>
              <td><span class="badge bg-label-success">Concluído</span></td>
              <td>2024-04-08 11:45</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection 