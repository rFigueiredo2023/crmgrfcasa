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
                    <div class="col-md-6">
                        <label class="form-label" for="razao_social">Razão Social</label>
                        <input type="text" class="form-control" id="razao_social" name="razao_social" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="cnpj">CNPJ</label>
                        <input type="text" class="form-control" id="cnpj" name="cnpj" required>
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
                    <div class="col-md-6">
                        <label class="form-label" for="telefone">Telefone</label>
                        <input type="text" class="form-control" id="telefone" name="telefone" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="celular">Celular</label>
                        <input type="text" class="form-control" id="celular" name="celular">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="email">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label" for="contato">Contato</label>
                        <input type="text" class="form-control" id="contato" name="contato" required>
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
        // Máscara para CNPJ
        document.getElementById('cnpj').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 14) {
                value = value.replace(/(\d{2})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1/$2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
                e.target.value = value;
            }
        });

        // Máscara para telefone
        document.getElementById('telefone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
                e.target.value = value;
            }
        });

        // Máscara para celular
        document.getElementById('celular').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{2})(\d)/, '($1) $2');
                value = value.replace(/(\d{5})(\d)/, '$1-$2');
                e.target.value = value;
            }
        });

        // Integração com API WebmaniaBR
        document.getElementById('cnpj').addEventListener('blur', function(e) {
            const cnpj = e.target.value.replace(/\D/g, '');
            if (cnpj.length === 14) {
                // Aqui você vai implementar a chamada para a API WebmaniaBR
                // Exemplo:
                /*
                fetch(`/api/consulta-cnpj/${cnpj}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('razao_social').value = data.razao_social;
                        document.getElementById('endereco').value = data.endereco;
                        // ... preencher outros campos
                    });
                */
            }
        });
    </script>
@endpush
