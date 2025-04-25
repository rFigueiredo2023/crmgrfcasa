{{-- View de Pages home relacionada a content/pages --}}
@php
    $configData = Helper::appClasses();
@endphp

@extends('layouts/horizontalLayout')

@section('title', 'Home')

@section('content')
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <h4 class="card-title">Bem-vindo ao Sistema</h4>
                    <p class="card-text">Gerencie seus clientes, transportadoras e veículos de forma eficiente.</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tutorialModal">
                        <i class="bx bx-info-circle me-1"></i>
                        Ver Tutorial de Introdução
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Tutorial do Sneat -->
    <div class="modal fade" id="tutorialModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tutorial de Introdução</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="carouselTutorial" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            <div class="carousel-item active text-center py-4">
                                <i class="bx bx-trophy text-primary mb-3" style="font-size: 4rem;"></i>
                                <h3>Bem-vindo ao Sistema!</h3>
                                <p class="mb-4">Este é um guia rápido para você conhecer as principais funcionalidades.
                                </p>
                            </div>
                            <div class="carousel-item text-center py-4">
                                <i class="bx bx-user-plus text-primary mb-3" style="font-size: 4rem;"></i>
                                <h3>Gerenciamento de Clientes</h3>
                                <p class="mb-4">Adicione, edite e gerencie seus clientes de forma simples e intuitiva.</p>
                            </div>
                            <div class="carousel-item text-center py-4">
                                <i class="bx bx-check-circle text-primary mb-3" style="font-size: 4rem;"></i>
                                <h3>Pronto para Começar!</h3>
                                <p class="mb-4">Agora você já pode começar a usar o sistema completo.</p>
                            </div>
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselTutorial"
                            data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselTutorial"
                            data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Próximo</span>
                        </button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Entendi</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Documento pronto, verificando modais');

            // Verificar se o Bootstrap está funcionando corretamente
            if (typeof bootstrap !== 'undefined') {
                console.log('Bootstrap está disponível');
            } else {
                console.warn('Bootstrap não está disponível!');
            }
        });
    </script>
@endpush
