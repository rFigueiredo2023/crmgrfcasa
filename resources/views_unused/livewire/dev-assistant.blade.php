<div>
    {{-- Botão de ativar/desativar o assistente --}}
    <div class="position-fixed bottom-0 end-0 m-4" style="z-index: 1050;">
        <button wire:click="toggleAssistant" class="btn btn-primary rounded-circle p-3 shadow-lg">
            <i class="bx {{ $showAssistant ? 'bx-x' : 'bx-brain' }} fs-4"></i>
        </button>
    </div>

    {{-- Modal do Assistente Dev --}}
    <div class="{{ $showAssistant ? 'show' : 'hide' }} position-fixed end-0 bottom-0 m-4" style="width: 400px; max-width: 90vw; z-index: 1040;">
        <div class="card shadow-lg border-0">
            <div class="card-header bg-primary d-flex justify-content-between align-items-center p-3">
                <h5 class="card-title text-white mb-0">
                    <i class="bx bx-brain me-2"></i>Assistente Dev
                </h5>
                <button wire:click="toggleAssistant" class="btn btn-sm btn-icon btn-text-secondary rounded-pill" aria-label="Fechar">
                    <i class="bx bx-x fs-4 text-white"></i>
                </button>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="ask">
                    <div class="mb-3">
                        <label for="prompt" class="form-label">Descreva o problema ou faça uma pergunta:</label>
                        <textarea wire:model.defer="prompt" id="prompt" class="form-control" rows="5" placeholder="Descreva o problema ou erro que você está enfrentando..."></textarea>
                        @error('prompt')
                            <div class="text-danger mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" wire:click="resetForm" class="btn btn-outline-secondary">
                            <i class="bx bx-reset me-1"></i>Limpar
                        </button>
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading wire:target="ask" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            <i wire:loading.remove wire:target="ask" class="bx bx-send me-1"></i>
                            Perguntar
                        </button>
                    </div>
                </form>

                @if(!empty($response))
                    <div class="mt-4">
                        <div class="divider">
                            <div class="divider-text">Resposta do Assistente</div>
                        </div>
                        <div class="p-3 bg-light rounded">
                            <div class="markdown-content">
                                {!! nl2br(e($response)) !!}
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Scripts para formatação de markdown --}}
    @push('scripts')
    <script>
        document.addEventListener('livewire:load', function() {
            // Função para formatar markdown se necessário
            function formatMarkdown() {
                // Se necessário adicionar bibliotecas externas para formatação de markdown
            }

            // Observar mudanças na resposta
            Livewire.hook('message.processed', (message, component) => {
                if (component.fingerprint.name === 'dev-assistant') {
                    formatMarkdown();
                }
            });
        });
    </script>
    @endpush
</div>
