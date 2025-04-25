{{-- Componente Form veiculo --}}
<!-- Offcanvas para adicionar veículo -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddVeiculo" aria-labelledby="offcanvasAddVeiculoLabel">
    <div class="offcanvas-header">
        <h5 id="offcanvasAddVeiculoLabel" class="offcanvas-title">Adicionar Veículo</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body mx-0 flex-grow-0">
        <form id="formAddVeiculo" class="add-new-veiculo pt-0" action="{{ route('veiculos.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="motorista">Motorista</label>
                <input type="text" class="form-control" id="motorista" name="motorista" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="marca">Marca</label>
                <input type="text" class="form-control" id="marca" name="marca" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="modelo">Modelo</label>
                <input type="text" class="form-control" id="modelo" name="modelo" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="ano_fabricacao">Ano de Fabricação</label>
                <input type="number" class="form-control" id="ano_fabricacao" name="ano_fabricacao" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="mes_licenca">Mês da Licença</label>
                <input type="text" class="form-control" id="mes_licenca" name="mes_licenca" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="local">Local</label>
                <input type="text" class="form-control" id="local" name="local" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="placa">Placa</label>
                <input type="text" class="form-control" id="placa" name="placa" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="uf">UF</label>
                <input type="text" class="form-control" id="uf" name="uf" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="tara">Tara (kg)</label>
                <input type="number" step="0.01" class="form-control" id="tara" name="tara" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="capacidade_kg">Capacidade em KG</label>
                <input type="number" step="0.01" class="form-control" id="capacidade_kg" name="capacidade_kg" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="capacidade_m3">Capacidade em M³</label>
                <input type="number" step="0.01" class="form-control" id="capacidade_m3" name="capacidade_m3" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="tipo_rodagem">Tipo de Rodagem</label>
                <select class="form-select" id="tipo_rodagem" name="tipo_rodagem" required>
                    <option value="">Selecione...</option>
                    <option value="truck">Truck</option>
                    <option value="toco">Toco</option>
                    <option value="cavalo_mecanico">Cavalo Mecânico</option>
                    <option value="van">Van</option>
                    <option value="utilitarios">Utilitários</option>
                    <option value="outros">Outros</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="tipo_carroceria">Tipo de Carroceria</label>
                <select class="form-select" id="tipo_carroceria" name="tipo_carroceria" required>
                    <option value="">Selecione...</option>
                    <option value="aberta">Aberta</option>
                    <option value="bau">Baú</option>
                    <option value="outros">Outros</option>
                    <option value="slider">Slider</option>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label" for="renavam">RENAVAM</label>
                <input type="text" class="form-control" id="renavam" name="renavam" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="cpf_cnpj_proprietario">CPF/CNPJ do Proprietário</label>
                <input type="text" class="form-control" id="cpf_cnpj_proprietario" name="cpf_cnpj_proprietario" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="proprietario">Proprietário</label>
                <input type="text" class="form-control" id="proprietario" name="proprietario" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="uf_proprietario">UF do Proprietário</label>
                <input type="text" class="form-control" id="uf_proprietario" name="uf_proprietario" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="tipo_proprietario">Tipo de Proprietário</label>
                <input type="text" class="form-control" id="tipo_proprietario" name="tipo_proprietario" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="detalhes">Detalhes</label>
                <textarea class="form-control" id="detalhes" name="detalhes" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary me-sm-3 me-1 data-submit">Salvar</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="offcanvas">Cancelar</button>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Máscara para placa
    document.getElementById('placa').addEventListener('input', function(e) {
        let value = e.target.value.toUpperCase();
        if (value.length <= 7) {
            value = value.replace(/([A-Z]{3})([0-9])/, '$1$2');
            value = value.replace(/([A-Z]{3}[0-9])([0-9])/, '$1$2');
            value = value.replace(/([A-Z]{3}[0-9]{2})([0-9])/, '$1$2');
            value = value.replace(/([A-Z]{3}[0-9]{3})([A-Z0-9])/, '$1$2');
            e.target.value = value;
        }
    });

    // Máscara para CPF/CNPJ do proprietário
    document.getElementById('cpf_cnpj_proprietario').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length <= 11) {
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1-$2');
        } else {
            value = value.replace(/(\d{2})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1/$2');
            value = value.replace(/(\d{4})(\d)/, '$1-$2');
        }
        e.target.value = value;
    });
</script>
@endpush
