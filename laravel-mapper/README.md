# Laravel Project Mapper

Este script foi criado para ajudar a mapear a estrutura completa de um projeto Laravel e identificar possíveis redundâncias, como views e modais não utilizados.

## O que este script faz

O script analisa seu projeto Laravel e gera relatórios detalhados sobre:

1. **Mapeamento completo** de views, controladores, models, migrations e componentes Livewire
2. **Identificação de views não utilizadas** que não são referenciadas em nenhum lugar do código
3. **Detecção de views com modais** para análise de componentes reutilizáveis
4. **Relações entre controladores e views** para entender o fluxo da aplicação
5. **Relações entre models** para visualizar a estrutura de dados do projeto

## Como usar

1. Copie o arquivo `laravel-mapper.sh` para a raiz do seu projeto Laravel
2. Torne o script executável: `chmod +x laravel-mapper.sh`
3. Execute o script: `./laravel-mapper.sh`
4. Os resultados serão gerados na pasta `laravel-mapper-results` dentro do seu projeto

## Resultados gerados

O script gera os seguintes arquivos:

- `README.md`: Resumo geral com estatísticas e recomendações
- `unused_views.txt`: Lista de views que não são referenciadas no código
- `view_references.md`: Mapeamento detalhado de cada view e onde é referenciada
- `controller_view_relations.md`: Relações entre controladores e views
- `model_relations.md`: Análise das relações entre models

## Limitações

- O script realiza uma análise estática e pode não detectar referências dinâmicas ou geradas em tempo de execução
- Recomenda-se revisar manualmente os resultados antes de tomar decisões de refatoração
- Views referenciadas via strings dinâmicas podem não ser detectadas corretamente

## Requisitos

- Projeto Laravel
- Bash shell (funciona no WSL como você mencionou)
- Comandos básicos como grep, find e awk (geralmente já disponíveis em ambientes Linux/WSL)
