{{-- Componente Form transportadora --}}
<!-- Modal para adicionar transportadora -->
<div class="modal fade" id="modalAddTransportadora" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-dialog-centered">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Adicionar Transportadora</h3>
                    <p class="text-muted">Preencha os dados da nova transportadora</p>
                </div>

                <form id="formAddTransportadora" class="row g-3" action="{{ route('transportadoras.store') }}"
                    method="POST">
                    @csrf
                    <!-- Dados preenchidos via API -->
                    <div class="col-md-6">
                        <label class="form-label" for="razao_social">Razão Social</label>
                        <input type="text" class="form-control" id="razao_social" name="razao_social" required>
                    </div>
                    <div class="col-md-6">
                        <x-campo-cnpj
                            id="campo-cnpj-transportadora"
                            :campos="[
                                'razao_social' => 'company.name',
                                'endereco' => 'address',
                                'codigo_ibge' => 'address.ibge_code'
                            ]"
                            modalSelector="#modalAddTransportadora"
                        />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="ie">Inscrição Estadual</label>
                        <input type="text" class="form-control" id="ie" name="inscricao_estadual">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="endereco">Endereço</label>
                        <input type="text" class="form-control" id="endereco" name="endereco" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="codigo_ibge">Código IBGE</label>
                        <input type="text" class="form-control" id="codigo_ibge" name="codigo_ibge" required>
                    </div>

                    <!-- Dados para o usuário preencher -->
                    <div class="col-md-6">
                        <label class="form-label" for="telefone">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone" required placeholder="Digite o telefone">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="celular">Celular</label>
                        <input type="text" class="form-control" id="celular" name="celular" placeholder="Digite o celular">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="email">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="Digite o email">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label" for="contato">Contato</label>
                        <input type="text" class="form-control" id="contato" name="contato" required placeholder="Digite o nome do contato">
                    </div>

                    <!-- Novo campo de observações -->
                    <div class="col-md-12">
                        <label class="form-label" for="observacoes">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3" placeholder="Observações adicionais sobre a transportadora"></textarea>
                    </div>

                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">Salvar</button>
                        <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        // Configurar evento quando o documento estiver pronto
        document.addEventListener('DOMContentLoaded', function() {
            // Referência ao modal
            const modalAddTransportadora = document.getElementById('modalAddTransportadora');

            // Adicionar evento quando o modal for aberto
            if (modalAddTransportadora) {
                modalAddTransportadora.addEventListener('shown.bs.modal', function() {
                    console.log('Modal transportadora aberto, configurando eventos...');

                    // Obter referências aos campos dentro do modal
                    const telefoneInput = modalAddTransportadora.querySelector('#telefone');
                    const celularInput = modalAddTransportadora.querySelector('#celular');

                    // Limpa os campos preenchidos pelo usuário ao abrir o modal
                    modalAddTransportadora.querySelector('#telefone').value = '';
                    modalAddTransportadora.querySelector('#celular').value = '';
                    modalAddTransportadora.querySelector('#email').value = '';
                    modalAddTransportadora.querySelector('#contato').value = '';
                    modalAddTransportadora.querySelector('#observacoes').value = '';

                    // Máscara para telefone
                    if (telefoneInput) {
                        telefoneInput.addEventListener('input', function(e) {
                            let value = e.target.value.replace(/\D/g, '');
                            if (value.length <= 11) {
                                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                                value = value.replace(/(\d{5})(\d)/, '$1-$2');
                                e.target.value = value;
                            }
                        });
                    }

                    // Máscara para celular
                    if (celularInput) {
                        celularInput.addEventListener('input', function(e) {
                            let value = e.target.value.replace(/\D/g, '');
                            if (value.length <= 11) {
                                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                                value = value.replace(/(\d{5})(\d)/, '$1-$2');
                                e.target.value = value;
                            }
                        });
                    }
                });
            }
        });
    </script>
@endpush
