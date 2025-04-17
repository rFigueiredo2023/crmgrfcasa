<!-- BEGIN: Theme CSS-->
<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet">

<!-- Icons -->
@vite([
  'resources/assets/vendor/fonts/boxicons.scss',
  'resources/assets/vendor/fonts/fontawesome.scss',
  'resources/assets/vendor/fonts/flag-icons.scss'
])

<!-- Core CSS -->
@vite(['resources/assets/vendor/scss/core.scss',
'resources/assets/vendor/scss/theme-default.scss',
'resources/assets/css/demo.css'])

<!-- Modal Fixes CSS -->
<link rel="stylesheet" href="{{ asset('css/modal-fix.css') }}" />

<!-- Layout Fixes CSS -->
<link rel="stylesheet" href="{{ asset('css/custom-fixes.css') }}" />

<!-- Vendors CSS -->
@vite([
  'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss',
  'resources/assets/vendor/libs/typeahead-js/typeahead.scss',
  'resources/assets/vendor/libs/apex-charts/apex-charts.scss',
  'resources/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.scss',
  'resources/assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.scss',
  'resources/assets/vendor/libs/sweetalert2/sweetalert2.scss'
])

<!-- Page CSS -->
@yield('vendor-style')

<!-- Page -->
@yield('page-style')
