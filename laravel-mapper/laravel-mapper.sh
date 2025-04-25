#!/bin/bash

# Laravel Project Mapper
# Este script mapeia a estrutura de um projeto Laravel e identifica elementos não utilizados

# Cores para saída
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[0;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}=== Laravel Project Mapper ===${NC}"
echo "Mapeando a estrutura do seu projeto Laravel..."

# Verificar se estamos em um projeto Laravel
if [ ! -f "artisan" ]; then
    echo -e "${RED}Erro: Este script deve ser executado na raiz de um projeto Laravel.${NC}"
    exit 1
fi

# Criar diretório para resultados
RESULT_DIR="laravel-mapper-results"
mkdir -p $RESULT_DIR

# Função para contar linhas de código
count_lines() {
    wc -l "$1" | awk '{print $1}'
}

# Mapear Views
echo -e "${BLUE}Mapeando Views...${NC}"
find ./resources/views -type f -name "*.blade.php" | sort > $RESULT_DIR/all_views.txt
echo "Total de Views: $(wc -l $RESULT_DIR/all_views.txt | awk '{print $1}')"

# Mapear Controladores
echo -e "${BLUE}Mapeando Controladores...${NC}"
find ./app/Http/Controllers -type f -name "*.php" | sort > $RESULT_DIR/all_controllers.txt
echo "Total de Controladores: $(wc -l $RESULT_DIR/all_controllers.txt | awk '{print $1}')"

# Mapear Models
echo -e "${BLUE}Mapeando Models...${NC}"
find ./app/Models -type f -name "*.php" | sort > $RESULT_DIR/all_models.txt
echo "Total de Models: $(wc -l $RESULT_DIR/all_models.txt | awk '{print $1}')"

# Mapear Migrations
echo -e "${BLUE}Mapeando Migrations...${NC}"
find ./database/migrations -type f -name "*.php" | sort > $RESULT_DIR/all_migrations.txt
echo "Total de Migrations: $(wc -l $RESULT_DIR/all_migrations.txt | awk '{print $1}')"

# Mapear Componentes Livewire (se existirem)
if [ -d "./app/Http/Livewire" ]; then
    echo -e "${BLUE}Mapeando Componentes Livewire...${NC}"
    find ./app/Http/Livewire -type f -name "*.php" | sort > $RESULT_DIR/all_livewire.txt
    echo "Total de Componentes Livewire: $(wc -l $RESULT_DIR/all_livewire.txt | awk '{print $1}')"
fi

# Identificar Views que contêm modais
echo -e "${BLUE}Identificando Views com modais...${NC}"
grep -l "modal" --include="*.blade.php" -r ./resources/views/ | sort > $RESULT_DIR/views_with_modals.txt
echo "Views com modais: $(wc -l $RESULT_DIR/views_with_modals.txt | awk '{print $1}')"

# Encontrar referências para cada view
echo -e "${YELLOW}Analisando referências para cada view...${NC}"
echo "Isso pode levar alguns minutos dependendo do tamanho do projeto..."

# Criar arquivo para views não utilizadas
> $RESULT_DIR/unused_views.txt
> $RESULT_DIR/view_references.md

echo "# Mapeamento de Referências de Views" > $RESULT_DIR/view_references.md
echo "" >> $RESULT_DIR/view_references.md
echo "| View | Referenciada em | Número de Referências |" >> $RESULT_DIR/view_references.md
echo "|------|----------------|------------------------|" >> $RESULT_DIR/view_references.md

while IFS= read -r view_path; do
    view_name=$(basename "$view_path")
    view_rel_path=${view_path#./resources/views/}
    view_rel_path=${view_rel_path%.blade.php}
    view_rel_path=${view_rel_path//\//.}
    
    # Procurar por referências diretas ao arquivo
    file_refs=$(grep -l "$view_name" --include="*.php" --include="*.blade.php" -r ./app ./resources | grep -v "$view_path" | sort)
    
    # Procurar por referências ao caminho da view (formato view('path.to.view'))
    view_refs=$(grep -l "view\(['\"]$view_rel_path['\"]" --include="*.php" -r ./app | sort)
    grep -l "View::make(['\"]$view_rel_path['\"]" --include="*.php" -r ./app | sort >> view_refs_temp.txt
    
    # Combinar resultados únicos
    cat view_refs_temp.txt 2>/dev/null >> view_refs_temp2.txt 2>/dev/null
    echo "$file_refs" >> view_refs_temp2.txt 2>/dev/null
    refs=$(cat view_refs_temp2.txt 2>/dev/null | sort | uniq | grep -v "^$")
    
    # Limpar arquivos temporários
    rm -f view_refs_temp.txt view_refs_temp2.txt 2>/dev/null
    
    ref_count=0
    if [ -n "$refs" ]; then
        ref_count=$(echo "$refs" | wc -l)
        ref_list=$(echo "$refs" | tr '\n' ', ' | sed 's/,$//')
        echo "| $view_rel_path | $ref_list | $ref_count |" >> $RESULT_DIR/view_references.md
    else
        echo "| $view_rel_path | *Nenhuma referência encontrada* | 0 |" >> $RESULT_DIR/view_references.md
        echo "$view_path" >> $RESULT_DIR/unused_views.txt
    fi
    
    echo -ne "Analisando views: $ref_count referências para $view_name\r"
done < $RESULT_DIR/all_views.txt

echo -e "\n${GREEN}Análise de views concluída!${NC}"

# Analisar controladores e suas relações com views
echo -e "${BLUE}Analisando relações entre controladores e views...${NC}"
> $RESULT_DIR/controller_view_relations.md

echo "# Relações entre Controladores e Views" > $RESULT_DIR/controller_view_relations.md
echo "" >> $RESULT_DIR/controller_view_relations.md
echo "| Controlador | Views Utilizadas |" >> $RESULT_DIR/controller_view_relations.md
echo "|-------------|------------------|" >> $RESULT_DIR/controller_view_relations.md

while IFS= read -r controller_path; do
    controller_name=$(basename "$controller_path")
    
    # Encontrar views referenciadas neste controlador
    views=$(grep -o "view(['\"][^'\"]*['\"]" "$controller_path" | sed "s/view(['\"]//g" | sed "s/['\"])//g" | sort | uniq)
    views2=$(grep -o "View::make(['\"][^'\"]*['\"]" "$controller_path" | sed "s/View::make(['\"]//g" | sed "s/['\"])//g" | sort | uniq)
    
    all_views="$views"
    if [ -n "$views2" ]; then
        all_views="$all_views"$'\n'"$views2"
    fi
    
    if [ -n "$all_views" ]; then
        view_list=$(echo "$all_views" | sort | uniq | tr '\n' ', ' | sed 's/,$//')
        echo "| $controller_name | $view_list |" >> $RESULT_DIR/controller_view_relations.md
    else
        echo "| $controller_name | *Nenhuma view diretamente referenciada* |" >> $RESULT_DIR/controller_view_relations.md
    fi
    
    echo -ne "Analisando controlador: $controller_name\r"
done < $RESULT_DIR/all_controllers.txt

echo -e "\n${GREEN}Análise de controladores concluída!${NC}"

# Analisar modelos e suas relações
echo -e "${BLUE}Analisando relações entre models...${NC}"
> $RESULT_DIR/model_relations.md

echo "# Relações entre Models" > $RESULT_DIR/model_relations.md
echo "" >> $RESULT_DIR/model_relations.md
echo "| Model | Relações |" >> $RESULT_DIR/model_relations.md
echo "|-------|----------|" >> $RESULT_DIR/model_relations.md

while IFS= read -r model_path; do
    model_name=$(basename "$model_path" .php)
    
    # Encontrar relações definidas neste modelo
    relations=$(grep -o "public function [a-zA-Z0-9_]*" "$model_path" | sed "s/public function //g" | sort)
    has_many=$(grep -o "hasMany" "$model_path" | wc -l)
    belongs_to=$(grep -o "belongsTo" "$model_path" | wc -l)
    has_one=$(grep -o "hasOne" "$model_path" | wc -l)
    many_to_many=$(grep -o "belongsToMany" "$model_path" | wc -l)
    
    relation_info=""
    if [ $has_many -gt 0 ]; then
        relation_info="$relation_info hasMany: $has_many,"
    fi
    if [ $belongs_to -gt 0 ]; then
        relation_info="$relation_info belongsTo: $belongs_to,"
    fi
    if [ $has_one -gt 0 ]; then
        relation_info="$relation_info hasOne: $has_one,"
    fi
    if [ $many_to_many -gt 0 ]; then
        relation_info="$relation_info belongsToMany: $many_to_many,"
    fi
    
    relation_info=$(echo "$relation_info" | sed 's/,$//')
    
    if [ -n "$relation_info" ]; then
        echo "| $model_name | $relation_info |" >> $RESULT_DIR/model_relations.md
    else
        echo "| $model_name | *Nenhuma relação encontrada* |" >> $RESULT_DIR/model_relations.md
    fi
    
    echo -ne "Analisando model: $model_name\r"
done < $RESULT_DIR/all_models.txt

echo -e "\n${GREEN}Análise de models concluída!${NC}"

# Gerar relatório final
echo -e "${BLUE}Gerando relatório final...${NC}"

cat > $RESULT_DIR/README.md << EOF
# Relatório de Mapeamento do Projeto Laravel

## Resumo

- **Total de Views**: $(wc -l $RESULT_DIR/all_views.txt | awk '{print $1}')
- **Views Não Utilizadas**: $(wc -l $RESULT_DIR/unused_views.txt | awk '{print $1}')
- **Views com Modais**: $(wc -l $RESULT_DIR/views_with_modals.txt | awk '{print $1}')
- **Total de Controladores**: $(wc -l $RESULT_DIR/all_controllers.txt | awk '{print $1}')
- **Total de Models**: $(wc -l $RESULT_DIR/all_models.txt | awk '{print $1}')
- **Total de Migrations**: $(wc -l $RESULT_DIR/all_migrations.txt | awk '{print $1}')
EOF

if [ -f "$RESULT_DIR/all_livewire.txt" ]; then
    echo "- **Total de Componentes Livewire**: $(wc -l $RESULT_DIR/all_livewire.txt | awk '{print $1}')" >> $RESULT_DIR/README.md
fi

cat >> $RESULT_DIR/README.md << EOF

## Arquivos Detalhados

- [Lista de Views Não Utilizadas](unused_views.txt)
- [Referências de Views](view_references.md)
- [Relações entre Controladores e Views](controller_view_relations.md)
- [Relações entre Models](model_relations.md)

## Views Não Utilizadas

As seguintes views parecem não estar sendo referenciadas em nenhum lugar do código:

\`\`\`
$(cat $RESULT_DIR/unused_views.txt)
\`\`\`

## Próximos Passos Recomendados

1. Revisar as views não utilizadas e decidir se devem ser removidas
2. Verificar se há controladores que não estão utilizando views (podem ser APIs)
3. Analisar as relações entre models para garantir que estão corretamente definidas
4. Considerar refatorar views com muitas referências para melhorar a manutenibilidade

EOF

echo -e "${GREEN}Relatório gerado com sucesso em $RESULT_DIR/README.md${NC}"
echo -e "${YELLOW}Nota: Este script faz uma análise estática e pode não detectar referências dinâmicas ou geradas em tempo de execução.${NC}"
echo -e "${YELLOW}Recomenda-se revisar manualmente os resultados antes de tomar decisões de refatoração.${NC}"
