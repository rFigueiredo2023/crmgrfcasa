<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\InscricaoEstadual;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class FormCliente extends Component
{
    // Propriedades básicas do cliente
    public $cliente_id;
    public $razao_social;
    public $cnpj;
    public $inscricao_estadual;
    public $telefone;
    public $contato;
    public $email;
    public $endereco;
    public $cep;
    public $municipio;
    public $uf;
    public $codigo_ibge;

    // Novas propriedades da API CNPJa
    public $nome_fantasia;
    public $fundacao;
    public $situacao;
    public $data_situacao;
    public $natureza_juridica;
    public $porte;
    public $capital_social;
    public $simples_nacional = false;
    public $logradouro;
    public $numero;
    public $bairro;
    public $cidade;
    public $estado;
    public $complemento;
    public $dominio_email;
    public $cnae_principal;
    public $cnaes_secundarios = [];
    public $socio_principal;
    public $funcao_socio;
    public $idade_socio;
    public $lista_socios = [];
    public $suframa;
    public $status_suframa;

    // Tipo contribuinte e regime tributário
    public $tipo_contribuinte = 'Contribuinte';
    public $regime_tributario = 'Simples Nacional';

    // Array de inscrições estaduais
    public $ies = [];

    // Mensagens
    public $message;
    public $error;

    // Status do carregamento
    public $loading = false;

    // Regras de validação
    protected $rules = [
        'razao_social' => 'required|string|max:255',
        'cnpj' => 'required|string|max:18',
        'telefone' => 'required|string|max:20',
        'contato' => 'required|string|max:255',
        'endereco' => 'required|string|max:255',
        'codigo_ibge' => 'required|string|max:10',
        'municipio' => 'required|string|max:100',
        'uf' => 'required|string|max:2',
    ];

    /**
     * Renderiza o componente
     */
    public function render()
    {
        return view('livewire.form-cliente');
    }

    /**
     * Limpa todos os campos do formulário
     */
    public function limparCampos()
    {
        $this->reset([
            'cliente_id', 'razao_social', 'cnpj', 'inscricao_estadual', 'telefone', 'contato',
            'email', 'endereco', 'cep', 'municipio', 'uf', 'codigo_ibge', 'nome_fantasia',
            'fundacao', 'situacao', 'data_situacao', 'natureza_juridica', 'porte', 'capital_social',
            'simples_nacional', 'logradouro', 'numero', 'bairro', 'cidade', 'estado', 'complemento',
            'dominio_email', 'cnae_principal', 'cnaes_secundarios', 'socio_principal', 'funcao_socio', 'idade_socio',
            'lista_socios', 'suframa', 'status_suframa', 'ies'
        ]);

        // Valores padrão
        $this->tipo_contribuinte = 'Contribuinte';
        $this->regime_tributario = 'Simples Nacional';

        // Limpa mensagens
        $this->message = '';
        $this->error = '';
    }

    /**
     * Carrega os dados de um cliente para edição
     */
    public function loadCliente($id)
    {
        $cliente = Cliente::with('inscricoesEstaduais')->findOrFail($id);

        // Carrega dados básicos
        $this->cliente_id = $cliente->id;
        $this->razao_social = $cliente->razao_social;
        $this->cnpj = $cliente->cnpj;
        $this->inscricao_estadual = $cliente->inscricao_estadual;
        $this->telefone = $cliente->telefone;
        $this->contato = $cliente->contato;
        $this->email = $cliente->email;
        $this->endereco = $cliente->endereco;
        $this->cep = $cliente->cep;
        $this->municipio = $cliente->municipio;
        $this->uf = $cliente->uf;
        $this->codigo_ibge = $cliente->codigo_ibge;

        // Carrega dados adicionais
        $this->nome_fantasia = $cliente->nome_fantasia;
        $this->fundacao = $cliente->fundacao ? $cliente->fundacao->format('Y-m-d') : null;
        $this->situacao = $cliente->situacao;
        $this->data_situacao = $cliente->data_situacao ? $cliente->data_situacao->format('Y-m-d') : null;
        $this->natureza_juridica = $cliente->natureza_juridica;
        $this->porte = $cliente->porte;
        $this->capital_social = $cliente->capital_social;
        $this->simples_nacional = $cliente->simples_nacional;
        $this->logradouro = $cliente->logradouro;
        $this->numero = $cliente->numero;
        $this->bairro = $cliente->bairro;
        $this->cidade = $cliente->cidade;
        $this->estado = $cliente->estado;
        $this->complemento = $cliente->complemento;
        $this->dominio_email = $cliente->dominio_email;
        $this->cnae_principal = $cliente->cnae_principal;
        $this->socio_principal = $cliente->socio_principal;
        $this->funcao_socio = $cliente->funcao_socio;
        $this->suframa = $cliente->suframa;
        $this->status_suframa = $cliente->status_suframa;
        $this->tipo_contribuinte = $cliente->tipo_contribuinte;
        $this->regime_tributario = $cliente->regime_tributario;

        // Carrega inscrições estaduais
        $this->ies = $cliente->inscricoesEstaduais->toArray();
    }

    /**
     * Função utilitária para consultar CNPJ na API CNPJa com logs detalhados
     *
     * @param string $cnpj CNPJ a ser consultado (somente números)
     * @return array|null Dados da empresa ou null em caso de erro
     * @throws \Exception Quando ocorre algum erro na requisição
     */
    protected function consultarCnpjCnpja($cnpj)
    {
        \Log::info('Iniciando consulta CNPJa', ['cnpj' => $cnpj]);

        try {
            // Token de autenticação da API CNPJa
            $apiToken = trim(config('services.cnpja.token'));
            $baseUrl = trim(config('services.cnpja.url', 'https://api.cnpja.com'));

            \Log::info('Configuração CNPJa', [
                'token_length' => strlen($apiToken),
                'token_first_chars' => substr($apiToken, 0, 5) . '...',
                'base_url' => $baseUrl
            ]);

            // Tentativa de fazer a requisição para a API CNPJa
            $url = "{$baseUrl}/office/{$cnpj}?registrations=BR&suframa=true";
            \Log::info('Enviando requisição para CNPJa', ['url' => $url]);

            $response = Http::withHeaders([
                'Authorization' => $apiToken
            ])->get($url);

            // Log da resposta HTTP (status e headers)
            \Log::info('Resposta HTTP da API CNPJa', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'successful' => $response->successful(),
                'failed' => $response->failed()
            ]);

            // Se a resposta contiver erro, registre o corpo da resposta
            if (!$response->successful()) {
                \Log::error('Erro na resposta CNPJa', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'json' => $response->json()
                ]);

                throw new \Exception(
                    'Erro ao consultar CNPJ: HTTP ' . $response->status() .
                    ' - ' . ($response->json()['message'] ?? $response->body())
                );
            }

            // Log de sucesso com informações básicas da resposta
            $data = $response->json();
            \Log::info('Consulta CNPJa bem-sucedida', [
                'razao_social' => $data['company']['name'] ?? 'N/A',
                'situacao' => $data['status']['text'] ?? 'N/A'
            ]);

            return $data;
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            \Log::error('Erro de conexão com a API CNPJa', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Erro de conexão com a API CNPJa: ' . $e->getMessage());
        } catch (\Illuminate\Http\Client\RequestException $e) {
            \Log::error('Erro na requisição para API CNPJa', [
                'message' => $e->getMessage(),
                'response' => $e->response->body() ?? 'Sem resposta',
                'trace' => $e->getTraceAsString()
            ]);
            throw new \Exception('Erro na requisição para API CNPJa: ' . $e->getMessage());
        } catch (\Exception $e) {
            \Log::error('Erro inesperado na consulta CNPJa', [
                'message' => $e->getMessage(),
                'class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Consulta o CNPJ na API CNPJa
     */
    public function consultarCnpj()
    {
        $this->loading = true;
        $this->message = '';
        $this->error = '';

        try {
            // Limpa o CNPJ mantendo apenas os números
            $cnpj = preg_replace('/\D/', '', $this->cnpj);

            // Verifica se o CNPJ tem 14 dígitos
            if (strlen($cnpj) !== 14) {
                $this->error = 'CNPJ inválido. Deve conter 14 dígitos.';
                $this->loading = false;
                return;
            }

            // Utilizando o novo método para consultar a API CNPJa
            $data = $this->consultarCnpjCnpja($cnpj);

            // Preenche os campos do formulário com os dados da API
            $this->razao_social = $data['company']['name'];
            $this->nome_fantasia = $data['alias'] ?? null;
            $this->fundacao = $data['founded'];
            $this->situacao = $data['status']['text'];
            $this->data_situacao = $data['statusDate'];
            $this->natureza_juridica = $data['company']['nature']['text'];
            $this->porte = $data['company']['size']['text'];
            $this->capital_social = $data['company']['equity'];
            $this->simples_nacional = $data['company']['simples']['optant'] ?? false;
            $this->logradouro = $data['address']['street'];
            $this->numero = $data['address']['number'];
            $this->bairro = $data['address']['district'];
            $this->cidade = $data['address']['city'];
            $this->estado = $data['address']['state'];
            $this->cep = $data['address']['zip'];
            $this->complemento = $data['address']['details'];
            $this->telefone = !empty($data['phones']) && isset($data['phones'][0]) ? $data['phones'][0]['area'] . $data['phones'][0]['number'] : null;
            $this->email = !empty($data['emails']) && isset($data['emails'][0]) ? $data['emails'][0]['address'] : null;
            $this->dominio_email = !empty($data['emails']) && isset($data['emails'][0]) ? $data['emails'][0]['domain'] : null;
            $this->cnae_principal = $data['mainActivity']['text'];

            // Carrega CNAEs secundários
            $this->cnaes_secundarios = [];
            if (!empty($data['sideActivities'])) {
                foreach ($data['sideActivities'] as $cnae) {
                    $this->cnaes_secundarios[] = $cnae['text'] ?? 'N/A';
                }
            }

            // Informações do sócio principal
            $this->socio_principal = !empty($data['company']['members']) && isset($data['company']['members'][0]) ? $data['company']['members'][0]['person']['name'] : null;
            $this->funcao_socio = !empty($data['company']['members']) && isset($data['company']['members'][0]) ? $data['company']['members'][0]['role']['text'] : null;
            $this->idade_socio = !empty($data['company']['members']) && isset($data['company']['members'][0]) ? $data['company']['members'][0]['person']['age'] : null;

            // Lista de sócios
            $this->lista_socios = [];
            if (!empty($data['company']['members'])) {
                foreach ($data['company']['members'] as $socio) {
                    $this->lista_socios[] = [
                        'nome' => $socio['person']['name'] ?? 'N/A',
                        'funcao' => $socio['role']['text'] ?? 'N/A',
                        'idade' => $socio['person']['age'] ?? 'N/A'
                    ];
                }
            }

            $this->suframa = !empty($data['suframa']) && isset($data['suframa'][0]) ? $data['suframa'][0]['number'] : null;
            $this->status_suframa = !empty($data['suframa']) && isset($data['suframa'][0]) ? $data['suframa'][0]['status']['text'] : null;

            // Preenche o endereço completo
            $this->endereco = "{$this->logradouro}, {$this->numero}";
            $this->municipio = $this->cidade;
            $this->uf = $this->estado;

            // Carrega as inscrições estaduais
            if (!empty($data['registrations'])) {
                $this->ies = [];
                foreach ($data['registrations'] as $ie) {
                    $this->ies[] = [
                        'estado' => $ie['state'],
                        'numero_ie' => $ie['number'],
                        'tipo_ie' => $ie['type']['text'],
                        'status_ie' => $ie['status']['text'],
                        'data_status_ie' => $ie['statusDate']
                    ];

                    // Se for a IE de SP, preenche o campo inscricao_estadual
                    if ($ie['state'] === 'SP') {
                        $this->inscricao_estadual = $ie['number'];
                    }
                }
            }

            // Define o regime tributário baseado no Simples Nacional
            if ($this->simples_nacional) {
                $this->regime_tributario = 'Simples Nacional';
            }

            $this->message = 'Dados do CNPJ carregados com sucesso!';
        } catch (\Exception $e) {
            $this->error = 'Erro ao consultar CNPJ: ' . $e->getMessage();
            \Log::error('Erro na consulta CNPJ (interface)', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        } finally {
            $this->loading = false;
        }
    }

    /**
     * Salva o cliente
     */
    public function save()
    {
        // Validação
        $this->validate();

        try {
            DB::beginTransaction();

            // Dados do cliente
            $clienteData = [
                'razao_social' => $this->razao_social,
                'cnpj' => $this->cnpj,
                'inscricao_estadual' => $this->inscricao_estadual,
                'telefone' => $this->telefone,
                'contato' => $this->contato,
                'email' => $this->email,
                'endereco' => $this->endereco,
                'cep' => $this->cep,
                'municipio' => $this->municipio,
                'uf' => $this->uf,
                'codigo_ibge' => $this->codigo_ibge,
                'nome_fantasia' => $this->nome_fantasia,
                'fundacao' => $this->fundacao,
                'situacao' => $this->situacao,
                'data_situacao' => $this->data_situacao,
                'natureza_juridica' => $this->natureza_juridica,
                'porte' => $this->porte,
                'capital_social' => $this->capital_social,
                'simples_nacional' => $this->simples_nacional,
                'logradouro' => $this->logradouro,
                'numero' => $this->numero,
                'bairro' => $this->bairro,
                'cidade' => $this->cidade,
                'estado' => $this->estado,
                'complemento' => $this->complemento,
                'dominio_email' => $this->dominio_email,
                'cnae_principal' => $this->cnae_principal,
                'socio_principal' => $this->socio_principal,
                'funcao_socio' => $this->funcao_socio,
                'suframa' => $this->suframa,
                'status_suframa' => $this->status_suframa,
                'tipo_contribuinte' => $this->tipo_contribuinte,
                'regime_tributario' => $this->regime_tributario,
                'user_id' => auth()->id()
            ];

            // Cria ou atualiza o cliente
            if ($this->cliente_id) {
                $cliente = Cliente::findOrFail($this->cliente_id);
                $cliente->update($clienteData);
                $this->message = 'Cliente atualizado com sucesso!';
            } else {
                $cliente = Cliente::create($clienteData);
                $this->message = 'Cliente criado com sucesso!';
            }

            // Salva as inscrições estaduais
            if (!empty($this->ies)) {
                // Remove todas as inscrições existentes para este cliente
                if ($this->cliente_id) {
                    InscricaoEstadual::where('cliente_id', $cliente->id)->delete();
                }

                // Cria as novas inscrições
                foreach ($this->ies as $ie) {
                    $cliente->inscricoesEstaduais()->create([
                        'estado' => $ie['estado'],
                        'numero_ie' => $ie['numero_ie'],
                        'tipo_ie' => $ie['tipo_ie'],
                        'status_ie' => $ie['status_ie'],
                        'data_status_ie' => $ie['data_status_ie']
                    ]);
                }
            }

            DB::commit();

            // Limpa o formulário após salvar
            if (!$this->cliente_id) {
                $this->limparCampos();
            }

            $this->dispatch('clienteSalvo');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error = 'Erro ao salvar cliente: ' . $e->getMessage();
        }
    }
}
