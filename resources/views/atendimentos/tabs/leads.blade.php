{{-- View de Leads relacionada a atendimentos/tabs --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Leads</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalNovoLeadAtendimento">
            <i class="bx bx-plus me-1"></i> Novo Lead
        </button>
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="input-group">
                    <input type="text" class="form-control" id="busca-lead" placeholder="Buscar lead...">
                    <button class="btn btn-outline-primary" type="button">
                        <i class="bx bx-search"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="table-responsive text-nowrap">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>RAZÃO SOCIAL</th>
                        <th>CNPJ</th>
                        <th>TELEFONE</th>
                        <th>CONTATO</th>
                        <th>ÚLTIMO ATENDIMENTO</th>
                        <th>VENDEDORA RESPONSÁVEL</th>
                        <th>ATENDIMENTO</th>
                        <th>HISTÓRICO</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse($leads as $lead)
                    <tr>
                        <td>{{ $lead->razao_social }}</td>
                        <td>{{ $lead->cnpj }}</td>
                        <td>{{ $lead->telefone }}</td>
                        <td>{{ $lead->contato }}</td>
                        <td>
                            @if($lead->ultimoAtendimento)
                                {{ $lead->ultimoAtendimento->created_at->format('d/m/Y H:i') }}
                            @else
                                Nenhum atendimento
                            @endif
                        </td>
                        <td>{{ $lead->vendedor->name ?? 'Não atribuído' }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary"
                                  data-bs-toggle="modal"
                                  data-bs-target="#modalAtendimento"
                                  data-cliente-id="{{ $lead->id }}"
                                  data-cliente-nome="{{ $lead->razao_social }}"
                                  data-tipo="lead">
                                <i class="bx bx-headphone me-1"></i>Atendimento
                            </button>
                        </td>
                        <td>
                            <button type="button"
                                    class="btn btn-icon btn-secondary lead-button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalHistorico"
                                    data-cliente-id="{{ $lead->id }}"
                                    data-cliente-nome="{{ $lead->razao_social }}"
                                    data-tipo="lead"
                                    title="Ver histórico completo">
                                <i class="bx bx-time"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center">Nenhum lead encontrado</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Botão de teste para diagnóstico do modal -->
<button type="button" class="btn btn-danger mt-3" id="btn-teste-modal">
    Testar Modal Manualmente
</button>

<script>
    // Função para abrir o modal de atendimento
    function inicializarBotoesAtendimento() {
        console.log('Função inicializarBotoesAtendimento chamada (está sendo substituída pelo código principal)');
        // Esta função está sendo substituída pelo código em pages-atendimentos.blade.php
        return true;
    }

    // Inicializa os botões quando o DOM estiver pronto
    document.addEventListener('DOMContentLoaded', function() {
        // Inicializar botões com jQuery
        if (typeof jQuery !== 'undefined') {
            jQuery('.btn-jquery-modal').on('click', function() {
                // Captura os dados do botão
                const id = jQuery(this).data('id');
                const nome = jQuery(this).data('nome');
                const tipo = jQuery(this).data('tipo');

                console.log('Botão jQuery clicado:', id, nome, tipo);

                // Configura os valores no formulário
                jQuery('#cliente_id').val(id);
                jQuery('#cliente_nome').val(nome);

                // Abre o modal com jQuery
                jQuery('#modalAtendimento').modal('show');
            });

            console.log('Botões jQuery inicializados');
        } else {
            console.error('jQuery não está disponível!');
        }

        // Máscara para telefone
        document.querySelectorAll('.phone-mask').forEach(function(element) {
            element.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 11) {
                    value = value.replace(/(\d{2})(\d)/, '($1) $2');
                    value = value.replace(/(\d{4,5})(\d)/, '$1-$2');
                    e.target.value = value;
                }
            });
        });

        // Máscara para CNPJ
        document.querySelectorAll('.cnpj-mask').forEach(function(element) {
            element.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length <= 14) {
                    value = value.replace(/(\d{2})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d)/, '$1.$2');
                    value = value.replace(/(\d{3})(\d)/, '$1/$2');
                    value = value.replace(/(\d{4})(\d)/, '$1-$2');
                    e.target.value = value;
                }
            });
        });

        // Função para confirmar exclusão
        function confirmarExclusao(id) {
            if (confirm('Tem certeza que deseja excluir este lead?')) {
                // Implementar lógica de exclusão
            }
        }

        // Busca de leads
        document.getElementById('busca-lead').addEventListener('input', function(e) {
            const busca = e.target.value.toLowerCase();
            const linhas = document.querySelectorAll('tbody tr');

            linhas.forEach(function(linha) {
                const texto = linha.textContent.toLowerCase();
                linha.style.display = texto.includes(busca) ? '' : 'none';
            });
        });

        // Botão de teste para abrir o modal diretamente
        document.getElementById('btn-teste-modal').addEventListener('click', function() {
            // Método 1: Usando jQuery (mais confiável)
            if (typeof jQuery !== 'undefined') {
                console.log('Tentando abrir modal com jQuery');
                jQuery('#modalAtendimento').modal('show');
            }
            // Método 2: Abordagem manual com Bootstrap
            else if (typeof bootstrap !== 'undefined') {
                console.log('Tentando abrir modal com bootstrap diretamente');
                const modal = document.getElementById('modalAtendimento');
                if (modal) {
                    const bsModal = new bootstrap.Modal(modal);
                    bsModal.show();
                } else {
                    console.error('Modal #modalAtendimento não encontrado no DOM');
                }
            }
            // Método 3: Força bruta com CSS
            else {
                console.log('Tentando abrir modal com força bruta');
                const modal = document.getElementById('modalAtendimento');
                if (modal) {
                    // Força estilos diretamente
                    modal.style.display = 'block';
                    modal.style.backgroundColor = 'rgba(0,0,0,0.5)';
                    modal.classList.add('show');
                    document.body.classList.add('modal-open');
                } else {
                    console.error('Modal #modalAtendimento não encontrado no DOM');
                }
            }
        });
    });
</script>

@include('components.modal-historico-cliente')

