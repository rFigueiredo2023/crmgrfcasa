# Relatório de Mapeamento do Projeto Laravel

## Resumo

- **Total de Views**: 77
- **Views Não Utilizadas**: 70
- **Views com Modais**: 35
- **Total de Controladores**: 27
- **Total de Models**: 11
- **Total de Migrations**: 21
- **Total de Componentes Livewire**: 1

## Arquivos Detalhados

- [Lista de Views Não Utilizadas](unused_views.txt)
- [Referências de Views](view_references.md)
- [Relações entre Controladores e Views](controller_view_relations.md)
- [Relações entre Models](model_relations.md)

## Views Não Utilizadas

As seguintes views parecem não estar sendo referenciadas em nenhum lugar do código:

```
./resources/views/_partials/components/form-lead-atendimento.blade.php
./resources/views/_partials/components/form-transportadora.blade.php
./resources/views/_partials/components/form-veiculo.blade.php
./resources/views/_partials/components/modal-atendimento.blade.php
./resources/views/_partials/logo.blade.php
./resources/views/_partials/macros.blade.php
./resources/views/atendimentos/index.blade.php
./resources/views/atendimentos/leads.blade.php
./resources/views/atendimentos/novo-lead.blade.php
./resources/views/atendimentos/tabs/lead.blade.php
./resources/views/atendimentos/tabs/leads.blade.php
./resources/views/atendimentos/tabs/novo-lead.blade.php
./resources/views/components/form-atendimento.blade.php
./resources/views/components/form-lead-atendimento.blade.php
./resources/views/components/form-transportadora.blade.php
./resources/views/components/form-veiculo.blade.php
./resources/views/components/forms/form-transportadora.blade.php
./resources/views/components/forms/form-veiculo.blade.php
./resources/views/components/modal-atendimento-lead.blade.php
./resources/views/components/modal-atendimento.blade.php
./resources/views/components/modal-historico-cliente.blade.php
./resources/views/components/modal-historico-lead.blade.php
./resources/views/components/offcanvas-add-cliente.blade.php
./resources/views/components/tabela-atendimentos.blade.php
./resources/views/components/tabela-clientes.blade.php
./resources/views/components/tabela-segmentos.blade.php
./resources/views/components/tabela-transportadoras.blade.php
./resources/views/components/tabela-veiculos.blade.php
./resources/views/content/authentications/auth-login-basic.blade.php
./resources/views/content/authentications/auth-register-basic.blade.php
./resources/views/content/clientes/index.blade.php
./resources/views/content/pages/atendimentos/edit.blade.php
./resources/views/content/pages/atendimentos/show.blade.php
./resources/views/content/pages/clientes/pages-clientes.blade.php
./resources/views/content/pages/customers/pages-customers.blade.php
./resources/views/content/pages/dashboard-vendas.blade.php
./resources/views/content/pages/leads/index.blade.php
./resources/views/content/pages/leads/pages-leads.blade.php
./resources/views/content/pages/pages-home.blade.php
./resources/views/content/pages/pages-misc-error.blade.php
./resources/views/content/pages/pages-page2.blade.php
./resources/views/content/pages/segmentos/index.blade.php
./resources/views/dashboards/admin.blade.php
./resources/views/dashboards/financeiro.blade.php
./resources/views/dashboards/financial.blade.php
./resources/views/dashboards/vendas.blade.php
./resources/views/dev-assistente.blade.php
./resources/views/layouts/app.blade.php
./resources/views/layouts/blankLayout.blade.php
./resources/views/layouts/commonMaster.blade.php
./resources/views/layouts/contentNavbarLayout.blade.php
./resources/views/layouts/horizontalLayout.blade.php
./resources/views/layouts/layoutFront.blade.php
./resources/views/layouts/layoutMaster.blade.php
./resources/views/layouts/sections/footer/footer-front.blade.php
./resources/views/layouts/sections/footer/footer.blade.php
./resources/views/layouts/sections/menu/horizontalMenu.blade.php
./resources/views/layouts/sections/menu/submenu.blade.php
./resources/views/layouts/sections/menu/verticalMenu.blade.php
./resources/views/layouts/sections/navbar/navbar-front.blade.php
./resources/views/layouts/sections/navbar/navbar.blade.php
./resources/views/layouts/sections/scripts.blade.php
./resources/views/layouts/sections/scriptsFront.blade.php
./resources/views/layouts/sections/scriptsIncludesFront.blade.php
./resources/views/layouts/sections/styles.blade.php
./resources/views/layouts/sections/stylesFront.blade.php
./resources/views/livewire/dev-assistant.blade.php
./resources/views/segmentos/create.blade.php
./resources/views/segmentos/edit.blade.php
./resources/views/segmentos/index.blade.php
```

## Próximos Passos Recomendados

1. Revisar as views não utilizadas e decidir se devem ser removidas
2. Verificar se há controladores que não estão utilizando views (podem ser APIs)
3. Analisar as relações entre models para garantir que estão corretamente definidas
4. Considerar refatorar views com muitas referências para melhorar a manutenibilidade

