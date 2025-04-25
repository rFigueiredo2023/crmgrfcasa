{{-- View de Financeiro relacionada a dashboards --}}
@extends('layouts.horizontalLayout')

@section('title', 'Dashboard Financeiro')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
<script>
  // Gráfico de Fluxo de Caixa
  let optionsFluxoCaixa = {
    chart: {
      height: 350,
      type: 'area',
      toolbar: {
        show: true
      }
    },
    dataLabels: {
      enabled: false
    },
    stroke: {
      curve: 'smooth'
    },
    series: [{
      name: 'Entradas',
      data: [31000, 40000, 28000, 51000, 42000, 82000, 56000]
    }, {
      name: 'Saídas',
      data: [11000, 32000, 45000, 32000, 34000, 52000, 41000]
    }],
    xaxis: {
      categories: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul'],
    },
    tooltip: {
      x: {
        format: 'dd/MM/yy HH:mm'
      },
    },
    colors: ['#696cff', '#ff6b6b']
  };

  // Gráfico de Despesas por Categoria
  let optionsDespesas = {
    chart: {
      height: 300,
      type: 'pie',
    },
    series: [44, 55, 13, 43, 22],
    labels: ['Pessoal', 'Marketing', 'Operacional', 'Impostos', 'Outros'],
    colors: ['#696cff', '#ff6b6b', '#05c3fb', '#ffab00', '#8592a3']
  };
  
  document.addEventListener('DOMContentLoaded', function() {
    let chartFluxoCaixa = new ApexCharts(document.querySelector("#chartFluxoCaixa"), optionsFluxoCaixa);
    let chartDespesas = new ApexCharts(document.querySelector("#chartDespesas"), optionsDespesas);
    chartFluxoCaixa.render();
    chartDespesas.render();
  });
</script>
@endsection

@section('content')
<h4>Dashboard Financeiro</h4>
<p class="mb-4">Visão geral financeira e relatórios.</p>

<!-- Aqui serão adicionados os componentes específicos para o financeiro -->

<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <div class="card">
      <div class="d-flex align-items-end row">
        <div class="col-sm-7">
          <div class="card-body">
            <h5 class="card-title text-primary">Bem-vindo, {{ auth()->user()->name }}! 🎉</h5>
            <p class="mb-4">Você tem <span class="fw-bold">2 relatórios</span> para analisar hoje.</p>
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

  <!-- Saldo em Caixa -->
  <div class="col-lg-4 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <h5 class="card-title mb-0">Saldo em Caixa</h5>
            <small class="card-text">Atualizado há 5 minutos</small>
            <div class="d-flex align-items-end mt-2">
              <h4 class="mb-0 me-2">R$ 234.850,00</h4>
              <small class="text-success">+2.8%</small>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-primary rounded p-2">
              <i class="bx bx-wallet bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Cards de Métricas -->
  <div class="col-lg-4 col-md-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Receitas do Mês</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="card-title mb-0 me-2">R$ 82.450</h4>
              <small class="text-success">(+12%)</small>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-success rounded p-2">
              <i class="bx bx-trending-up bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Despesas do Mês</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="card-title mb-0 me-2">R$ 52.000</h4>
              <small class="text-danger">(+8%)</small>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-danger rounded p-2">
              <i class="bx bx-trending-down bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 col-md-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div class="card-info">
            <p class="card-text">Contas a Receber</p>
            <div class="d-flex align-items-end mb-2">
              <h4 class="card-title mb-0 me-2">R$ 28.450</h4>
              <small class="text-warning">(15)</small>
            </div>
          </div>
          <div class="card-icon">
            <span class="badge bg-label-warning rounded p-2">
              <i class="bx bx-calendar bx-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Fluxo de Caixa -->
  <div class="col-md-8 mb-4">
    <div class="card">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title mb-0">Fluxo de Caixa</h5>
        <div class="dropdown">
          <button class="btn p-0" type="button" id="fluxoCaixaOptions" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="bx bx-dots-vertical-rounded"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="fluxoCaixaOptions">
            <a class="dropdown-item" href="javascript:void(0);">Ver Relatório</a>
            <a class="dropdown-item" href="javascript:void(0);">Exportar PDF</a>
            <a class="dropdown-item" href="javascript:void(0);">Atualizar</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div id="chartFluxoCaixa"></div>
      </div>
    </div>
  </div>

  <!-- Despesas por Categoria -->
  <div class="col-md-4 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Despesas por Categoria</h5>
      </div>
      <div class="card-body">
        <div id="chartDespesas"></div>
      </div>
    </div>
  </div>

  <!-- Últimas Transações -->
  <div class="col-12 mb-4">
    <div class="card">
      <div class="card-header">
        <h5 class="card-title mb-0">Últimas Transações</h5>
      </div>
      <div class="table-responsive text-nowrap">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Data</th>
              <th>Descrição</th>
              <th>Categoria</th>
              <th>Valor</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            <tr>
              <td>#5089</td>
              <td>14/03/2024</td>
              <td>Pagamento Fornecedor XYZ</td>
              <td>Operacional</td>
              <td class="text-danger">- R$ 4.890,00</td>
              <td><span class="badge bg-label-success">Concluído</span></td>
            </tr>
            <tr>
              <td>#5088</td>
              <td>14/03/2024</td>
              <td>Recebimento Cliente ABC</td>
              <td>Vendas</td>
              <td class="text-success">+ R$ 12.450,00</td>
              <td><span class="badge bg-label-success">Concluído</span></td>
            </tr>
            <tr>
              <td>#5087</td>
              <td>13/03/2024</td>
              <td>Pagamento Marketing Digital</td>
              <td>Marketing</td>
              <td class="text-danger">- R$ 2.500,00</td>
              <td><span class="badge bg-label-warning">Pendente</span></td>
            </tr>
            <tr>
              <td>#5086</td>
              <td>13/03/2024</td>
              <td>Impostos Municipais</td>
              <td>Impostos</td>
              <td class="text-danger">- R$ 1.890,00</td>
              <td><span class="badge bg-label-success">Concluído</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection 