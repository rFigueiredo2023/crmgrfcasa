<!DOCTYPE html>
<html>
<head>
    <!-- ... -->
</head>
<body>
    <!-- Conteúdo principal -->
    @yield('content')

    <!-- Seção específica para modais -->
    @yield('modals')

    <!-- Scripts -->
    @stack('scripts')
</body>
</html>