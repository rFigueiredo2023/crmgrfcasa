/**
 * Script de diagnóstico para a API de CNPJ
 * Use para testar rotas e configurações
 */

// IIFE para evitar poluição do escopo global
(function() {
    // Executa quando o DOM está pronto
    document.addEventListener('DOMContentLoaded', function() {
        console.log('=== DIAGNÓSTICO DE CNPJ INICIADO ===');

        // Informações sobre a página
        console.log('URL atual:', window.location.href);
        console.log('Hostname:', window.location.hostname);
        console.log('Pathname:', window.location.pathname);

        // Testar rotas disponíveis com um CNPJ de teste
        const cnpjTeste = '20922582000170';
        const rotas = [
            '/consultar-cnpj/' + cnpjTeste,
            '/api/consultar-cnpj/' + cnpjTeste,
            '/customers/api/consultar-cnpj/' + cnpjTeste
        ];

        // Captura os elementos do botão e resultados de diagnóstico, ou cria se não existirem
        let diagButton = document.getElementById('cnpj-diag-button');
        let diagResults = document.getElementById('cnpj-diag-results');

        if (!diagButton) {
            diagButton = document.createElement('button');
            diagButton.id = 'cnpj-diag-button';
            diagButton.textContent = 'Diagnóstico CNPJ';
            diagButton.style.position = 'fixed';
            diagButton.style.bottom = '10px';
            diagButton.style.right = '10px';
            diagButton.style.zIndex = '9999';
            diagButton.style.padding = '8px 15px';
            diagButton.style.backgroundColor = '#007bff';
            diagButton.style.color = 'white';
            diagButton.style.border = 'none';
            diagButton.style.borderRadius = '4px';
            diagButton.style.cursor = 'pointer';
            document.body.appendChild(diagButton);
        }

        if (!diagResults) {
            diagResults = document.createElement('div');
            diagResults.id = 'cnpj-diag-results';
            diagResults.style.position = 'fixed';
            diagResults.style.bottom = '50px';
            diagResults.style.right = '10px';
            diagResults.style.width = '400px';
            diagResults.style.maxHeight = '60vh';
            diagResults.style.overflowY = 'auto';
            diagResults.style.backgroundColor = 'rgba(0,0,0,0.8)';
            diagResults.style.color = 'white';
            diagResults.style.padding = '15px';
            diagResults.style.borderRadius = '5px';
            diagResults.style.fontFamily = 'monospace';
            diagResults.style.fontSize = '12px';
            diagResults.style.display = 'none';
            diagResults.style.zIndex = '9998';
            document.body.appendChild(diagResults);
        }

        // Função para adicionar texto ao painel de resultados
        function addResult(text, type = 'info') {
            let line = document.createElement('div');
            line.textContent = text;

            if (type === 'error') {
                line.style.color = '#ff6b6b';
            } else if (type === 'success') {
                line.style.color = '#51cf66';
            } else if (type === 'warning') {
                line.style.color = '#fcc419';
            }

            diagResults.appendChild(line);
            diagResults.scrollTop = diagResults.scrollHeight;
        }

        // Função para testar uma rota
        async function testarRota(rota) {
            addResult(`Testando rota: ${rota}...`);

            try {
                const response = await fetch(rota);
                const status = response.status;

                addResult(`Status: ${status}`, status >= 200 && status < 300 ? 'success' : 'error');

                try {
                    const text = await response.text();
                    addResult(`Tamanho da resposta: ${text.length} caracteres`, 'info');

                    try {
                        // Tenta parsear como JSON
                        JSON.parse(text);
                        addResult('Resposta é um JSON válido', 'success');
                    } catch (e) {
                        // Se não for JSON, mostra os primeiros 100 caracteres
                        const preview = text.substring(0, 100) + (text.length > 100 ? '...' : '');
                        addResult(`Resposta não é JSON: ${preview}`, 'error');
                    }
                } catch (e) {
                    addResult(`Erro ao ler resposta: ${e.message}`, 'error');
                }
            } catch (e) {
                addResult(`Erro de rede: ${e.message}`, 'error');
            }

            addResult('------------------------------');
        }

        // Evento de clique para o botão
        diagButton.addEventListener('click', async function() {
            diagResults.innerHTML = '';
            diagResults.style.display = 'block';

            addResult('=== DIAGNÓSTICO DE CNPJ ===');
            addResult(`Data/hora: ${new Date().toLocaleString()}`);
            addResult(`URL atual: ${window.location.href}`);
            addResult('------------------------------');

            // Verificar disponibilidade de funções e bibliotecas
            addResult('Verificando dependências:');
            addResult(`SweetAlert2: ${typeof Swal !== 'undefined' ? 'Disponível' : 'Indisponível'}`,
                      typeof Swal !== 'undefined' ? 'success' : 'warning');
            addResult(`jQuery: ${typeof jQuery !== 'undefined' ? 'Disponível (v' + jQuery.fn.jquery + ')' : 'Indisponível'}`,
                      typeof jQuery !== 'undefined' ? 'success' : 'warning');
            addResult(`buscarCNPJSimples: ${typeof buscarCNPJSimples === 'function' ? 'Disponível' : 'Indisponível'}`,
                      typeof buscarCNPJSimples === 'function' ? 'success' : 'error');
            addResult('------------------------------');

            // Testar todas as rotas
            for (const rota of rotas) {
                await testarRota(rota);
            }

            addResult('=== DIAGNÓSTICO CONCLUÍDO ===');
        });

        // Exibe instruções no console
        console.log('Diagnóstico CNPJ pronto! Clique no botão "Diagnóstico CNPJ" no canto inferior direito para iniciar os testes.');
    });
})();
