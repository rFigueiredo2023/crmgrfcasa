/**
 * Bootstrap Modal Initialization
 *
 * Este script é usado para inicializar corretamente modais do Bootstrap
 * prevenindo problemas com a propriedade backdrop.
 */
document.addEventListener('DOMContentLoaded', function () {
  // Verifica se o objeto bootstrap está disponível
  if (typeof bootstrap !== 'undefined') {
    console.log('Bootstrap carregado com sucesso');

    // Inicializa todos os modais na página com opções personalizadas
    var modalElems = document.querySelectorAll('.modal');
    modalElems.forEach(function (modalElem) {
      // Configura opções para evitar problemas com o backdrop
      var modalOptions = {
        backdrop: true,
        keyboard: true,
        focus: true
      };

      try {
        var modal = new bootstrap.Modal(modalElem, modalOptions);
        console.log('Modal inicializado: ', modalElem.id);

        // Armazena a instância no elemento para referência futura
        modalElem._bsModal = modal;
      } catch (e) {
        console.error('Erro ao inicializar modal: ', e);
      }
    });
  } else {
    console.error('Bootstrap não foi carregado corretamente');
  }
});
