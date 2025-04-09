<!-- Modal -->
<div class="modal fade" id="basicModal" tabindex="-1" aria-labelledby="basicModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="basicModalLabel">
          <i class="bx bx-user-plus me-2"></i> Adicionar Cliente
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <form action="{{ route('clientes.store') }}" method="POST" id="formAddCliente">
          @csrf
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label" for="razao_social">Razão Social</label>
              <input type="text" id="razao_social" name="razao_social" class="form-control" required />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label" for="cnpj">CNPJ</label>
              <input type="text" id="cnpj" name="cnpj" class="form-control" required />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label" for="ie">Inscrição Estadual</label>
              <input type="text" id="ie" name="ie" class="form-control" />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label" for="telefone">Telefone</label>
              <input type="text" id="telefone" name="telefone" class="form-control" required />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label" for="contato">Contato</label>
              <input type="text" id="contato" name="contato" class="form-control" required />
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label" for="codigo_ibge">Código IBGE</label>
              <input type="text" id="codigo_ibge" name="codigo_ibge" class="form-control" required />
            </div>
            <div class="col-md-12 mb-3">
              <label class="form-label" for="endereco">Endereço</label>
              <input type="text" id="endereco" name="endereco" class="form-control" required />
            </div>
          </div>

          <div class="modal-footer mt-3">
            <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Salvar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
