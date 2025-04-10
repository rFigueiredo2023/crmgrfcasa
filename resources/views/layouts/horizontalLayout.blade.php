@isset($pageConfigs)
    {!! Helper::updatePageConfig($pageConfigs) !!}
@endisset

@php
    $configData = Helper::appClasses();

    $menuHorizontal = true;
    $navbarFull = true;

    $isNavbar = $isNavbar ?? true;
    $isMenu = $isMenu ?? true;
    $isFlex = $isFlex ?? false;
    $isFooter = $isFooter ?? true;
    $customizerHidden = $customizerHidden ?? '';

    $menuFixed = $configData['menuFixed'] ?? '';
    $navbarType = $configData['navbarType'] ?? '';
    $footerFixed = $configData['footerFixed'] ?? '';
    $menuCollapsed = $configData['menuCollapsed'] ?? '';

    $container = $configData['contentLayout'] === 'compact' ? 'container-xxl' : 'container-fluid';
    $containerNav = $configData['contentLayout'] === 'compact' ? 'container-xxl' : 'container-fluid';
@endphp

@extends('layouts/commonMaster')

@section('layoutContent')
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container">

            {{-- Navbar --}}
            @if ($isNavbar)
                @include('layouts/sections/navbar/navbar')
            @endif

            {{-- Página principal --}}
            <div class="layout-page">

                {{-- Content wrapper --}}
                <div class="content-wrapper">

                    @if ($isMenu)
                        @include('layouts/sections/menu/horizontalMenu')
                    @endif

                    {{-- Conteúdo da página --}}
                    @if ($isFlex)
                        <div class="{{ $container }} d-flex align-items-stretch flex-grow-1 p-0">
                        @else
                            <div class="{{ $container }} flex-grow-1 container-p-y">
                    @endif

                    @yield('content')

                </div>
                {{-- /Conteúdo --}}

                {{-- Rodapé --}}
                @if ($isFooter)
                    @include('layouts/sections/footer/footer')
                @endif

                <div class="content-backdrop fade"></div>
            </div>
            {{-- /Content wrapper --}}
        </div>
        {{-- /Layout page --}}
    </div>
    {{-- /Layout container --}}

    @stack('modals')

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>
    </div>
    {{-- /Layout wrapper --}}
@endsection

<head>
    <!-- ... outros meta tags ... -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
