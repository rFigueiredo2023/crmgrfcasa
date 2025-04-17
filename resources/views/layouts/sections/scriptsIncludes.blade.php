@php
use Illuminate\Support\Facades\Vite;

$menuCollapsed = ($configData['menuCollapsed'] === 'layout-menu-collapsed') ? json_encode(true) : false;
@endphp

<!-- Fix para o problema de "piscada" do layout durante o carregamento -->
<script>
  // Sobrescreve a função que recalcula o padding-top dinamicamente
  // DEVE ser definido ANTES do carregamento do helpers.js
  window.Helpers = window.Helpers || {};
  window.Helpers.isNavbarFixed = function () {
    return false;
  };
</script>

<!-- laravel style -->
@vite(['resources/assets/vendor/js/helpers.js'])
<!-- beautify ignore:start -->
@if ($configData['hasCustomizer'])
  <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
  <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
  @vite(['resources/assets/vendor/js/template-customizer.js'])
@endif

  <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
  @vite(['resources/assets/js/config.js'])

@if ($configData['hasCustomizer'])
<script type="module">
  window.templateCustomizer = new TemplateCustomizer({
    cssPath: '',
    themesPath: '',
    defaultStyle: "light", // Forçar tema claro
    defaultShowDropdownOnHover: "{{$configData['showDropdownOnHover']}}", // true/false (for horizontal layout only)
    displayCustomizer: false, // Desativar o customizador
    lang: '{{ app()->getLocale() }}',
    pathResolver: function(path) {
      var resolvedPaths = {
        // Core stylesheets
        @foreach (['core'] as $name)
          '{{ $name }}.scss': '{{ Vite::asset('resources/assets/vendor/scss'.$configData["rtlSupport"].'/'.$name.'.scss') }}',
          '{{ $name }}-dark.scss': '{{ Vite::asset('resources/assets/vendor/scss'.$configData["rtlSupport"].'/'.$name.'-dark.scss') }}',
        @endforeach

        // Themes
        @foreach (['default', 'bordered', 'semi-dark'] as $name)
          'theme-{{ $name }}.scss': '{{ Vite::asset('resources/assets/vendor/scss'.$configData["rtlSupport"].'/theme-'.$name.'.scss') }}',
          'theme-{{ $name }}-dark.scss': '{{ Vite::asset('resources/assets/vendor/scss'.$configData["rtlSupport"].'/theme-'.$name.'-dark.scss') }}',
        @endforeach
      }
      return resolvedPaths[path] || path;
    },
    transitions: false, // Desativar transições
    styles: ['light'], // Permitir apenas o estilo claro
    layout: 'horizontal', // Fixar o layout como horizontal
    layoutType: 'fixed', // Fixar o tipo de layout
    navbarType: 'fixed', // Fixar a navbar
    'controls': ['style'] // Permitir apenas controle de estilo (e mesmo assim, limitado ao claro)
  });
</script>
@endif
