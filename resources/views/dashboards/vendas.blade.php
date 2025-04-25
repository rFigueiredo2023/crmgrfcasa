{{-- View de Vendas relacionada a dashboards --}}
@extends('layouts.horizontalLayout')

@section('title', 'Dashboard de Vendas')

@section('vendor-style')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/apex-charts/apex-charts.css')}}" />
@endsection

@section('vendor-script')
<script src="{{asset('assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
@endsection

@section('page-script')
<script>
  // Gráfico de Metas
  let optionsMetas = {
    chart: {
      height: 250,
      type: 'radialBar',
    },
    series: [75],
    labels: ['Meta Mensal'],
    colors: ['#696cff']
  };

  // Gráfico de Vendas por Categoria
  let optionsCategoria = {
    chart: {
      height: 300,
      type: 'donut',
    },
    series: [44, 55, 13, 43],
    labels: ['Eletrônicos', 'Roupas', 'Alimentos', 'Outros'],
    colors: ['#696cff', '#ff6b6b', '#05c3fb', '#ffab00']
  };
  
  document.addEventListener('DOMContentLoaded', function() {
    let chartMetas = new ApexCharts(document.querySelector("#chartMetas"), optionsMetas);
    let chartCategoria = new ApexCharts(document.querySelector("#chartCategoria"), optionsCategoria);
    chartMetas.render();
    chartCategoria.render();
  });
</script>
@endsection

@section('content')
<h4>Dashboard de Vendas</h4>
<p class="mb-4">Visão geral das vendas e métricas.</p>

<!-- Aqui serão adicionados os componentes específicos para vendas -->
@endsection 