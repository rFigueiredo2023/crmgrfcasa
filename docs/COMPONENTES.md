# Documentação de Componentes - CRM GRF

## Componentes Blade

Este documento contém informações sobre os componentes Blade disponíveis no sistema e como usá-los corretamente em novos desenvolvimentos.

### CampoCnpj

O componente `campo-cnpj` oferece um campo de entrada de CNPJ com consulta automática à API de CNPJ e preenchimento automático de outros campos do formulário.

#### Propriedades

| Propriedade    | Tipo   | Obrigatório | Descrição                                        |
|----------------|--------|-------------|--------------------------------------------------|
| id             | string | Sim         | ID único para o campo (evita conflitos)          |
| campos         | array  | Não         | Mapeamento de campos do formulário para API      |
| formSelector   | string | Não         | Seletor CSS do formulário pai                    |
| modalSelector  | string | Não         | Seletor CSS do modal pai                         |
| class          | string | Não         | Classes CSS adicionais                           |
| placeholder    | string | Não         | Texto de placeholder personalizado               |
| value          | string | Não         | Valor inicial do campo                           |

#### Estrutura do Mapeamento de Campos

O mapeamento de campos (`campos`) é um array que associa seletores de campos do formulário às propriedades retornadas pela API de CNPJ:

```php
[
  'id_do_campo_no_formulario' => 'caminho.para.propriedade.na.api',
  // ...
]
```

#### Propriedades Comuns da API

| Caminho na API            | Descrição                      | Exemplo                         |
|---------------------------|--------------------------------|---------------------------------|
| company.name              | Razão social                   | "EMPRESA EXEMPLO LTDA"          |
| company.trade             | Nome fantasia                  | "EXEMPLO"                       |
| company.type              | Tipo de empresa                | "SOCIEDADE EMPRESÁRIA LIMITADA" |
| company.size.acronym      | Porte da empresa (ME, EPP...) | "ME"                            |
| company.email             | Email                          | "contato@exemplo.com.br"        |
| address.street            | Logradouro                     | "RUA EXEMPLO"                   |
| address.number            | Número                         | "123"                           |
| address.details           | Complemento                    | "SALA 1"                        |
| address.district          | Bairro                         | "CENTRO"                        |
| address.city              | Município                      | "SÃO PAULO"                     |
| address.state             | UF                             | "SP"                            |
| address.zip               | CEP                            | "12345-678"                     |
| mainActivity.text         | Atividade principal            | "COMÉRCIO VAREJISTA"            |
| phones.0.number           | Telefone principal             | "(11) 1234-5678"                |

#### Exemplo de Uso

```blade
<x-campo-cnpj 
  id="cnpj-cliente"
  :campos="[
    'razao_social' => 'company.name',
    'email' => 'company.email',
    'endereco' => 'address',
    'municipio' => 'address.city',
    'uf' => 'address.state',
    'cep' => 'address.zip'
  ]"
  modalSelector="#clienteModal"
  placeholder="Digite o CNPJ"
/>
```

#### Comportamentos Especiais

O componente possui tratamentos especiais para alguns tipos de campos:

1. **Endereço**: Quando o campo é mapeado para `address`, o componente monta um endereço completo combinando rua, número, complemento e bairro.

2. **Código IBGE**: Se não for encontrado diretamente na API, o componente tentará buscar automaticamente na BrasilAPI usando o município e UF.

3. **Inscrição Estadual**: Procura em diferentes formatos da API e preenche automaticamente o campo `inscricao_estadual` ou `ie`.

4. **Atividades Secundárias**: Se existir um container com id `atividades_secundarias`, o componente preencherá com as atividades retornadas pela API.

#### Boas Práticas

1. Sempre defina um `id` único para cada instância do componente.

2. Use o seletor de modal ou formulário para restringir o escopo de busca dos campos.

3. Inclua apenas os campos que realmente precisam ser preenchidos no mapeamento.

4. Use este componente em todos os formulários que precisam de consulta de CNPJ para manter a consistência do sistema. 
