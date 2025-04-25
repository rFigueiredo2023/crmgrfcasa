{{-- Formulário de Form cliente --}}
<div>
    <form wire:submit.prevent="save">
        @if($message)
            <div class="alert alert-success">{{ $message }}</div>
        @endif

        @if($error)
            <div class="alert alert-danger">{{ $error }}</div>
        @endif

        <input type="hidden" wire:model="cliente_id">

        <!-- Campo CNPJ com botão de consulta -->
        <div class="mb-3 row">
            <div class="col-md-6">
                <label for="cnpj" class="form-label">CNPJ *</label>
                <div class="input-group">
                    <input type="text" class="form-control @error('cnpj') is-invalid @enderror"
                           id="cnpj" wire:model="cnpj" placeholder="00.000.000/0000-00" required>
                    <button class="btn btn-outline-primary" type="button" wire:click="consultarCnpj"
                            wire:loading.attr="disabled" wire:target="consultarCnpj">
                        <span wire:loading.remove wire:target="consultarCnpj">Consultar</span>
                        <span wire:loading wire:target="consultarCnpj">
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Consultando...
                        </span>
                    </button>
                </div>
                @error('cnpj') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <!-- Seção: Identificação -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">🧾 Identificação</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="razao_social" class="form-label">Razão Social *</label>
                        <input type="text" class="form-control @error('razao_social') is-invalid @enderror"
                               id="razao_social" wire:model="razao_social" required>
                        @error('razao_social') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="nome_fantasia" class="form-label">Nome Fantasia</label>
                        <input type="text" class="form-control" id="nome_fantasia" wire:model="nome_fantasia">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="situacao" class="form-label">Situação Cadastral</label>
                        <input type="text" class="form-control" id="situacao" wire:model="situacao" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="data_situacao" class="form-label">Data da Situação</label>
                        <input type="date" class="form-control" id="data_situacao" wire:model="data_situacao" readonly>
                    </div>
                    <div class="col-md-4">
                        <label for="fundacao" class="form-label">Data de Fundação</label>
                        <input type="date" class="form-control" id="fundacao" wire:model="fundacao">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="natureza_juridica" class="form-label">Natureza Jurídica</label>
                        <input type="text" class="form-control" id="natureza_juridica" wire:model="natureza_juridica">
                    </div>
                    <div class="col-md-6">
                        <label for="porte" class="form-label">Porte</label>
                        <input type="text" class="form-control" id="porte" wire:model="porte">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="capital_social" class="form-label">Capital Social</label>
                        <input type="text" class="form-control" id="capital_social" wire:model="capital_social">
                    </div>
                    <div class="col-md-6">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" id="simples_nacional"
                                   wire:model="simples_nacional">
                            <label class="form-check-label" for="simples_nacional">
                                Optante pelo Simples Nacional
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção: Endereço -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">📍 Endereço</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="cep" class="form-label">CEP</label>
                        <input type="text" class="form-control" id="cep" wire:model="cep">
                    </div>
                    <div class="col-md-6">
                        <label for="logradouro" class="form-label">Logradouro (Rua)</label>
                        <input type="text" class="form-control" id="logradouro" wire:model="logradouro">
                    </div>
                    <div class="col-md-2">
                        <label for="numero" class="form-label">Número</label>
                        <input type="text" class="form-control" id="numero" wire:model="numero">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="complemento" class="form-label">Complemento</label>
                        <input type="text" class="form-control" id="complemento" wire:model="complemento">
                    </div>
                    <div class="col-md-4">
                        <label for="bairro" class="form-label">Bairro</label>
                        <input type="text" class="form-control" id="bairro" wire:model="bairro">
                    </div>
                    <div class="col-md-4">
                        <label for="endereco" class="form-label">Endereço Completo *</label>
                        <input type="text" class="form-control @error('endereco') is-invalid @enderror"
                               id="endereco" wire:model="endereco" required>
                        @error('endereco') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="municipio" class="form-label">Município *</label>
                        <input type="text" class="form-control @error('municipio') is-invalid @enderror"
                               id="municipio" wire:model="municipio" required>
                        @error('municipio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="uf" class="form-label">UF *</label>
                        <input type="text" class="form-control @error('uf') is-invalid @enderror"
                               id="uf" wire:model="uf" maxlength="2" required>
                        @error('uf') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-4">
                        <label for="codigo_ibge" class="form-label">Código IBGE *</label>
                        <input type="text" class="form-control @error('codigo_ibge') is-invalid @enderror"
                               id="codigo_ibge" wire:model="codigo_ibge" required>
                        @error('codigo_ibge') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção: Contato -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">📞 Contato</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="telefone" class="form-label">Telefone *</label>
                        <input type="text" class="form-control @error('telefone') is-invalid @enderror"
                               id="telefone" wire:model="telefone" required>
                        @error('telefone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" wire:model="email">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="contato" class="form-label">Contato *</label>
                        <input type="text" class="form-control @error('contato') is-invalid @enderror"
                               id="contato" wire:model="contato" required>
                        @error('contato') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="dominio_email" class="form-label">Domínio do E-mail</label>
                        <input type="text" class="form-control" id="dominio_email" wire:model="dominio_email">
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção: Atividade Econômica -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">📊 Atividade Econômica</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="cnae_principal" class="form-label">CNAE Principal</label>
                        <input type="text" class="form-control" id="cnae_principal" wire:model="cnae_principal">
                    </div>
                </div>

                <!-- CNAEs Secundários -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">CNAEs Secundários</label>
                        <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
                            @if(!empty($cnaes_secundarios) && count($cnaes_secundarios) > 0)
                                <ul class="list-group list-group-flush">
                                    @foreach($cnaes_secundarios as $cnae)
                                        <li class="list-group-item">{{ $cnae }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted mb-0">Não há CNAEs secundários registrados.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção: Sociedade -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">🧍‍♂️ Sociedade</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="socio_principal" class="form-label">Sócio Principal</label>
                        <input type="text" class="form-control" id="socio_principal" wire:model="socio_principal">
                    </div>
                    <div class="col-md-6">
                        <label for="funcao_socio" class="form-label">Função do Sócio</label>
                        <input type="text" class="form-control" id="funcao_socio" wire:model="funcao_socio">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="idade_socio" class="form-label">Idade aproximada do Sócio</label>
                        <input type="text" class="form-control" id="idade_socio" wire:model="idade_socio" readonly>
                    </div>
                </div>

                <!-- Lista de Sócios -->
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">Lista de Sócios</label>
                        <div class="border rounded p-2" style="max-height: 200px; overflow-y: auto;">
                            @if(!empty($lista_socios) && count($lista_socios) > 0)
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Nome</th>
                                                <th>Função</th>
                                                <th>Idade</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($lista_socios as $socio)
                                                <tr>
                                                    <td>{{ $socio['nome'] ?? 'N/A' }}</td>
                                                    <td>{{ $socio['funcao'] ?? 'N/A' }}</td>
                                                    <td>{{ $socio['idade'] ?? 'N/A' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-muted mb-0">Não há outros sócios registrados.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Seção: SUFRAMA (visível apenas para empresas de Manaus/AM) -->
        <div class="card mb-4" id="secao-suframa" x-data="{ showSuframa: '{{ strtoupper($uf) }}' === 'AM' || '{{ $suframa }}' !== '' }">
            <div class="card-header">
                <h5 class="mb-0">📦 SUFRAMA</h5>
                <small class="text-muted">Somente relevante para empresas com sede em Manaus/AM</small>
            </div>
            <div class="card-body" x-show="showSuframa">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="suframa" class="form-label">Número SUFRAMA</label>
                        <input type="text" class="form-control" id="suframa" wire:model="suframa">
                    </div>
                    <div class="col-md-6">
                        <label for="status_suframa" class="form-label">Status SUFRAMA</label>
                        <input type="text" class="form-control" id="status_suframa" wire:model="status_suframa">
                    </div>
                </div>
            </div>
            <div class="card-body bg-light" x-show="!showSuframa">
                <p class="text-muted mb-0">Seção não aplicável. A empresa não está sediada em Manaus/AM.</p>
            </div>
        </div>

        <!-- Seção: Inscrição Estadual -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">🏛️ Inscrição Estadual</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="inscricao_estadual" class="form-label">Inscrição Estadual (SP)</label>
                        <input type="text" class="form-control" id="inscricao_estadual" wire:model="inscricao_estadual" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="tipo_contribuinte" class="form-label">Tipo de Contribuinte *</label>
                        <select class="form-select" id="tipo_contribuinte" wire:model="tipo_contribuinte">
                            <option value="Contribuinte">Contribuinte</option>
                            <option value="Não Contribuinte">Não Contribuinte</option>
                            <option value="Isento">Isento</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="regime_tributario" class="form-label">Regime Tributário *</label>
                        <select class="form-select" id="regime_tributario" wire:model="regime_tributario">
                            <option value="Simples Nacional">Simples Nacional</option>
                            <option value="Lucro Presumido">Lucro Presumido</option>
                            <option value="Lucro Real">Lucro Real</option>
                        </select>
                    </div>
                </div>

                @if(count($ies) > 0)
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Estado</th>
                                    <th>Número IE</th>
                                    <th>Tipo</th>
                                    <th>Status</th>
                                    <th>Data Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ies as $ie)
                                    <tr>
                                        <td>{{ $ie['estado'] }}</td>
                                        <td>{{ $ie['numero_ie'] }}</td>
                                        <td>{{ $ie['tipo_ie'] }}</td>
                                        <td>{{ $ie['status_ie'] }}</td>
                                        <td>{{ $ie['data_status_ie'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <button type="button" class="btn btn-secondary" wire:click="limparCampos">Limpar</button>
            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Salvar Cliente</span>
                <span wire:loading wire:target="save">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Salvando...
                </span>
            </button>
        </div>
    </form>
</div>
