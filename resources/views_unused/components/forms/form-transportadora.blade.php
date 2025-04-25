{{-- Componente Form transportadora --}}
<!-- Offcanvas para adicionar transportadora -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddTransportadora" aria-labelledby="offcanvasAddTransportadoraLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasAddTransportadoraLabel" class="offcanvas-title">Adicionar Transportadora</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
        <form id="formAddTransportadora" class="add-new-transportadora pt-0" action="{{ route('transportadoras.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="razao_social">Razão Social</label>
                <input type="text" class="form-control" id="razao_social" name="razao_social" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="cnpj">CNPJ</label>
                <input type="text" class="form-control" id="cnpj" name="cnpj" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="ie">Inscrição Estadual</label>
                <input type="text" class="form-control" id="ie" name="ie">
            </div>
            <div class="mb-3">
                <label class="form-label" for="endereco">Endereço</label>
                <input type="text" class="form-control" id="endereco" name="endereco" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="codigo_ibge">Código IBGE</label>
                <input type="text" class="form-control" id="codigo_ibge" name="codigo_ibge" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="telefone">Telefone</label>
                <input type="text" class="form-control" id="telefone" name="telefone" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="celular">Celular</label>
                <input type="text" class="form-control" id="celular" name="celular">
            </div>
            <div class="mb-3">
                <label class="form-label" for="contato">Contato</label>
                <input type="text" class="form-control" id="contato" name="contato" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="email">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Salvar</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancelar</button>
        </form>
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
