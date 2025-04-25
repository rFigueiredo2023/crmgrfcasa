#!/bin/bash

# Script de Reorganização do Projeto Laravel CRM
# Este script reorganiza a estrutura de um projeto Laravel CRM conforme as melhores práticas
# e consolida componentes duplicados

# Cores para saída
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[0;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}=== Laravel CRM Reorganizer ===${NC}"
echo "Reorganizando a estrutura do seu projeto Laravel CRM..."

# Verificar se estamos em um projeto Laravel
if [ ! -f "artisan" ]; then
    echo -e "${RED}Erro: Este script deve ser executado na raiz de um projeto Laravel.${NC}"
    exit 1
fi

# Criar diretório para backup
BACKUP_DIR="resources/views_backup_$(date +%Y%m%d_%H%M%S)"
mkdir -p $BACKUP_DIR

echo -e "${BLUE}Criando backup das views atuais em $BACKUP_DIR...${NC}"
cp -r resources/views/* $BACKUP_DIR/

# Criar nova estrutura de diretórios conforme solicitado
echo -e "${BLUE}Criando nova estrutura de diretórios...${NC}"

# Diretórios principais
mkdir -p resources/views/clientes
mkdir -p resources/views/atendimentos
mkdir -p resources/views/segmentos
mkdir -p resources/views/leads
mkdir -p resources/views/components
mkdir -p resources/views/_partials
mkdir -p resources/views/layouts

# Diretório temporário para views não utilizadas
mkdir -p resources/views_unused

# Função para verificar se um arquivo existe
file_exists() {
    if [ -f "$1" ]; then
        return 0
    else
        return 1
    fi
}

# Função para consolidar componentes duplicados
consolidate_component() {
    local component_type=$1
    local component_name=$2
    local target_dir=$3
    local source_files=()
    
    # Encontrar todas as versões do componente
    while IFS= read -r file; do
        source_files+=("$file")
    done < <(find ./resources/views -type f -name "*${component_name}*.blade.php" | grep -v "$BACKUP_DIR")
    
    if [ ${#source_files[@]} -eq 0 ]; then
        echo -e "${YELLOW}Nenhum componente ${component_name} encontrado.${NC}"
        return
    fi
    
    # Se houver apenas um arquivo, apenas mova-o
    if [ ${#source_files[@]} -eq 1 ]; then
        local target_file="${target_dir}/${component_name}.blade.php"
        echo "Movendo ${source_files[0]} para ${target_file}"
        mkdir -p "$(dirname "$target_file")"
        cp "${source_files[0]}" "$target_file"
        return
    fi
    
    # Se houver múltiplos arquivos, use o mais recente como base
    local newest_file=""
    local newest_time=0
    
    for file in "${source_files[@]}"; do
        local file_time=$(stat -c %Y "$file")
        if [ $file_time -gt $newest_time ]; then
            newest_time=$file_time
            newest_file=$file
        fi
    done
    
    local target_file="${target_dir}/${component_name}.blade.php"
    echo "Consolidando ${#source_files[@]} versões de ${component_name} usando ${newest_file} como base"
    mkdir -p "$(dirname "$target_file")"
    cp "$newest_file" "$target_file"
    
    # Mover os outros arquivos para unused
    for file in "${source_files[@]}"; do
        if [ "$file" != "$newest_file" ]; then
            local unused_path="resources/views_unused/$(basename "$file")"
            cp "$file" "$unused_path"
            echo "  Versão alternativa movida para: $unused_path"
        fi
    done
}

# Consolidar componentes de clientes
echo -e "${BLUE}Consolidando componentes de clientes...${NC}"
consolidate_component "form" "form-cliente" "resources/views/clientes"
consolidate_component "modal" "modal-cliente" "resources/views/clientes"
consolidate_component "tabela" "tabela-clientes" "resources/views/components"

# Consolidar componentes de transportadoras
echo -e "${BLUE}Consolidando componentes de transportadoras...${NC}"
consolidate_component "form" "form-transportadora" "resources/views/transportadoras"
consolidate_component "modal" "modal-transportadora" "resources/views/transportadoras"
consolidate_component "tabela" "tabela-transportadoras" "resources/views/components"

# Consolidar componentes de atendimentos
echo -e "${BLUE}Consolidando componentes de atendimentos...${NC}"
consolidate_component "form" "form-atendimento" "resources/views/atendimentos"
consolidate_component "modal" "modal-atendimento" "resources/views/atendimentos"
consolidate_component "tabela" "tabela-atendimentos" "resources/views/components"

# Consolidar componentes de segmentos
echo -e "${BLUE}Consolidando componentes de segmentos...${NC}"
consolidate_component "form" "form-segmento" "resources/views/segmentos"
consolidate_component "tabela" "tabela-segmentos" "resources/views/components"

# Consolidar componentes de veículos
echo -e "${BLUE}Consolidando componentes de veículos...${NC}"
consolidate_component "form" "form-veiculo" "resources/views/veiculos"
consolidate_component "tabela" "tabela-veiculos" "resources/views/components"

# Consolidar componentes de leads
echo -e "${BLUE}Consolidando componentes de leads...${NC}"
consolidate_component "form" "form-lead" "resources/views/leads"
consolidate_component "modal" "modal-lead" "resources/views/leads"

# Mover componentes comuns
echo -e "${BLUE}Organizando componentes comuns...${NC}"

# Mover layouts
if [ -d "resources/views/layouts" ]; then
    echo "Mantendo layouts existentes"
    # Garantir que os layouts principais estejam no lugar certo
    for layout in app blankLayout; do
        find ./resources/views -name "${layout}.blade.php" -not -path "*/layouts/*" | while read file; do
            echo "Movendo layout $file para resources/views/layouts/"
            cp "$file" "resources/views/layouts/$(basename "$file")"
        done
    done
fi

# Mover partials
echo "Organizando partials..."
for partial in navbar footer; do
    find ./resources/views -name "*${partial}*.blade.php" | while read file; do
        echo "Movendo partial $file para resources/views/_partials/"
        cp "$file" "resources/views/_partials/$(basename "$file")"
    done
done

# Mover componentes genéricos
echo "Organizando componentes genéricos..."
for component in input alert button card; do
    find ./resources/views -name "*${component}*.blade.php" | while read file; do
        echo "Movendo componente $file para resources/views/components/"
        cp "$file" "resources/views/components/$(basename "$file")"
    done
done

# Mover views principais para seus respectivos diretórios
echo -e "${BLUE}Organizando views principais...${NC}"

# Clientes
find ./resources/views -path "*/clientes/*index*.blade.php" | while read file; do
    echo "Movendo view de clientes $file para resources/views/clientes/"
    cp "$file" "resources/views/clientes/index.blade.php"
done

# Atendimentos
find ./resources/views -path "*/atendimentos/*index*.blade.php" | while read file; do
    echo "Movendo view de atendimentos $file para resources/views/atendimentos/"
    cp "$file" "resources/views/atendimentos/index.blade.php"
done

# Segmentos
find ./resources/views -path "*/segmentos/*index*.blade.php" | while read file; do
    echo "Movendo view de segmentos $file para resources/views/segmentos/"
    cp "$file" "resources/views/segmentos/index.blade.php"
done

# Leads
find ./resources/views -path "*/leads/*index*.blade.php" | while read file; do
    echo "Movendo view de leads $file para resources/views/leads/"
    cp "$file" "resources/views/leads/index.blade.php"
done

# Mover views não utilizadas para o diretório de backup
echo -e "${BLUE}Movendo views não utilizadas para backup...${NC}"

# Ler a lista de views não utilizadas
if [ -f "laravel-mapper-results/unused_views.txt" ]; then
    while IFS= read -r view_path; do
        if [ -f "$view_path" ]; then
            # Criar estrutura de diretórios no backup
            rel_path=${view_path#./resources/views/}
            backup_path="resources/views_unused/$rel_path"
            mkdir -p "$(dirname "$backup_path")"
            
            # Copiar arquivo para backup
            cp "$view_path" "$backup_path"
            echo "View não utilizada movida para backup: $backup_path"
        fi
    done < "laravel-mapper-results/unused_views.txt"
else
    echo -e "${YELLOW}Arquivo de views não utilizadas não encontrado. Pulando esta etapa.${NC}"
fi

echo -e "${GREEN}Reorganização concluída com sucesso!${NC}"
echo ""
echo -e "${BLUE}Resumo:${NC}"
echo "1. Backup completo das views originais criado em: $BACKUP_DIR"
echo "2. Nova estrutura de diretórios criada conforme solicitado"
echo "3. Componentes duplicados consolidados"
echo "4. Views não utilizadas movidas para: resources/views_unused/"
echo ""
echo -e "${YELLOW}Próximos passos:${NC}"
echo "1. Verifique a nova estrutura e certifique-se de que tudo está correto"
echo "2. Atualize as referências às views nos seus controladores e outros arquivos"
echo "3. Teste a aplicação para garantir que tudo funciona corretamente"
echo "4. Após confirmar que tudo está funcionando, você pode remover o diretório de backup"
echo ""
echo -e "${RED}IMPORTANTE:${NC} Este script cria cópias dos arquivos na nova estrutura, mantendo os originais."
echo "Você precisará atualizar manualmente as referências nos controladores e outros arquivos."
