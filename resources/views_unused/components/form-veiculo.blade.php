{{-- Componente Form veiculo --}}
<!-- Modal para adicionar veículo -->
<div class="modal fade" id="modalAddVeiculo" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-simple modal-dialog-centered">
        <div class="modal-content p-3 p-md-5">
            <div class="modal-body">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="text-center mb-4">
                    <h3 class="mb-2">Adicionar Veículo</h3>
                    <p class="text-muted">Preencha os dados do novo veículo</p>
                </div>

                <form id="formAddVeiculo" class="row g-3" action="{{ route('veiculos.store') }}" method="POST">
                    @csrf
                    <div class="col-md-6">
                        <label class="form-label" for="motorista">Motorista</label>
                        <input type="text" class="form-control" id="motorista" name="motorista" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="marca">Marca</label>
                        <input type="text" class="form-control" id="marca" name="marca" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="modelo">Modelo</label>
                        <input type="text" class="form-control" id="modelo" name="modelo" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="ano_fabricacao">Ano de Fabricação</label>
                        <input type="number" class="form-control" id="ano_fabricacao" name="ano_fabricacao" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="chassi">Chassi</label>
                        <input type="text" class="form-control" id="chassi" name="chassi">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="mes_licenca">Mês da Licença</label>
                        <input type="text" class="form-control" id="mes_licenca" name="mes_licenca" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="local">Local</label>
                        <input type="text" class="form-control" id="local" name="local" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="placa">Placa</label>
                        <input type="text" class="form-control" id="placa" name="placa" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="uf">UF</label>
                        <input type="text" class="form-control" id="uf" name="uf" value="SP" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="km_oleo">KM Óleo</label>
                        <input type="number" class="form-control" id="km_oleo" name="km_oleo">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="km_correia">KM Correia</label>
                        <input type="number" class="form-control" id="km_correia" name="km_correia">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="segurado_ate">Segurado até</label>
                        <input type="date" class="form-control" id="segurado_ate" name="segurado_ate">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="limite_km_mes">Limite KM/Mês</label>
                        <input type="number" class="form-control" id="limite_km_mes" name="limite_km_mes">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="tara">Tara (kg)</label>
                        <input type="number" step="0.01" class="form-control" id="tara" name="tara" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="capacidade_kg">Capacidade em KG</label>
                        <input type="number" step="0.01" class="form-control" id="capacidade_kg" name="capacidade_kg" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="capacidade_m3">Capacidade em M³</label>
                        <input type="number" step="0.01" class="form-control" id="capacidade_m3" name="capacidade_m3" required>
                    </div>
                    <div class="col-md-6">
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
                    <div class="col-md-6">
                        <label class="form-label" for="tipo_carroceria">Tipo de Carroceria</label>
                        <select class="form-select" id="tipo_carroceria" name="tipo_carroceria" required>
                            <option value="">Selecione...</option>
                            <option value="aberta">Aberta</option>
                            <option value="fechada-bau">Fechada/Baú</option>
                            <option value="granelera">Graneleira</option>
                            <option value="porta-container">Porta Container</option>
                            <option value="slider">Slider</option>
                            <option value="outros">Outros</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="renavam">RENAVAM</label>
                        <input type="text" class="form-control" id="renavam" name="renavam" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="responsavel_atual">Responsável Atual</label>
                        <input type="text" class="form-control" id="responsavel_atual" name="responsavel_atual">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="cpf_cnpj_proprietario">CPF/CNPJ do Proprietário</label>
                        <input type="text" class="form-control" id="cpf_cnpj_proprietario" name="cpf_cnpj_proprietario" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="proprietario">Proprietário</label>
                        <input type="text" class="form-control" id="proprietario" name="proprietario" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="antt_rntrc">ANTT / RNTRC</label>
                        <input type="text" class="form-control" id="antt_rntrc" name="antt_rntrc">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="uf_proprietario">UF do Proprietário</label>
                        <input type="text" class="form-control" id="uf_proprietario" name="uf_proprietario" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="ie_proprietario">IE do Proprietário</label>
                        <input type="text" class="form-control" id="ie_proprietario" name="ie_proprietario">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="tipo_proprietario">Tipo de Proprietário</label>
                        <select class="form-select" id="tipo_proprietario" name="tipo_propriedade" required>
                            <option value="">Selecione...</option>
                            <option value="agregado">Agregado</option>
                            <option value="independente">Independente</option>
                            <option value="locacao">Locação</option>
                            <option value="proprio">Próprio</option>
                            <option value="outros">Outros</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="detalhes">Detalhes</label>
                        <textarea class="form-control" id="detalhes" name="detalhes" rows="2"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="transportadora_id">Transportadora</label>
                        <select class="form-select" id="transportadora_id" name="transportadora_id">
                            <option value="">Selecione uma transportadora...</option>
                            @foreach(\App\Models\Transportadora::orderBy('razao_social')->get() as $transportadora)
                                <option value="{{ $transportadora->id }}">{{ $transportadora->razao_social }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary me-sm-3 me-1">Salvar</button>
                        <button type="reset" class="btn btn-label-secondary"
                            data-bs-dismiss="modal">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
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
