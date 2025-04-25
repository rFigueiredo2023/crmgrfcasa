{{-- Layout Scripts --}}
<!-- BEGIN: Vendor JS-->

@vite([
  'resources/assets/vendor/libs/jquery/jquery.js',
  'resources/assets/vendor/libs/popper/popper.js',
  'resources/assets/vendor/js/bootstrap.js',
  'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js',
  'resources/assets/vendor/libs/hammer/hammer.js',
  'resources/assets/vendor/libs/typeahead-js/typeahead.js',
  'resources/assets/vendor/js/menu.js'
])

<!-- Verifica se o jQuery e o Bootstrap estão carregados corretamente -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Verificar se o jQuery está carregado
    if (typeof jQuery !== 'undefined') {
      console.log('jQuery carregado com sucesso, versão:', jQuery.fn.jquery);
    } else {
      console.error('jQuery NÃO foi carregado!');
    }

    // Verificar se o Bootstrap está carregado
    setTimeout(function() {
      if (typeof bootstrap !== 'undefined') {
        console.log('Bootstrap carregado com sucesso', typeof bootstrap.Modal !== 'undefined' ?
                   ', versão Modal: ' + bootstrap.Modal.VERSION : '');
      } else {
        console.error('Bootstrap NÃO foi carregado corretamente!');
      }
    }, 100);
  });
</script>

@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
@vite(['resources/assets/js/main.js'])
<!-- Carregamento explícito do bootstrap-init.js -->
<script src="{{ asset('js/bootstrap-init.js') }}"></script>
<!-- Correção para os modais -->
<script src="{{ asset('js/modal-fix.js') }}"></script>
<!-- Carregamento do gerenciador de modais -->
<script src="{{ asset('js/modal-handler.js') }}"></script>

<!-- END: Theme JS-->
<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->
<!-- BEGIN: Page JS-->
@yield('page-script')
<!-- END: Page JS-->

<!-- Removido o fix do padding-top que agora está em scriptsIncludes.blade.php -->

<!-- Remover configurações salvas do customizador -->
<script>
  // Remover opções salvas no localStorage para evitar customizações
  localStorage.removeItem('templateCustomizer-options');

  // Função para garantir que o tema claro seja sempre aplicado
  document.addEventListener('DOMContentLoaded', function() {
    document.documentElement.setAttribute('data-theme', 'theme-default');
    document.documentElement.classList.remove('dark-style');
    document.documentElement.classList.add('light-style');

    // Impedir que o customizador altere o tema
    if (window.templateCustomizer) {
      window.templateCustomizer.settings.availableThemes = ['theme-default'];
      window.templateCustomizer.settings.defaultStyle = 'theme-default';
      window.templateCustomizer.settings.displayCustomizer = false;
    }
  });
</script>

<!-- Scripts de libs, vendors etc -->

<!-- Scripts de consulta CNPJ -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/cnpj-consulta.js') }}"></script>
<script src="{{ asset('assets/js/cnpj-diagnose.js') }}"></script>

@yield('script')
