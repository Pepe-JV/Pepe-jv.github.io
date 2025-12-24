#!/bin/bash

# Script de instalación rápida para Database Backup CLI Tool
# Por Jose Justicia

echo "╔═══════════════════════════════════════════════════════╗"
echo "║     DATABASE BACKUP CLI TOOL - INSTALACIÓN          ║"
echo "╚═══════════════════════════════════════════════════════╝"
echo ""

# Colores
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Verificar PHP
echo -e "${YELLOW}[1/5]${NC} Verificando PHP..."
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v | head -n 1)
    echo -e "${GREEN}✓${NC} PHP encontrado: $PHP_VERSION"
else
    echo -e "${RED}✗${NC} PHP no está instalado"
    echo "Por favor instala PHP 7.4 o superior"
    exit 1
fi

# Dar permisos de ejecución
echo -e "${YELLOW}[2/5]${NC} Configurando permisos..."
chmod +x backup.php
echo -e "${GREEN}✓${NC} Permisos configurados"

# Crear directorios necesarios
echo -e "${YELLOW}[3/5]${NC} Creando directorios..."
mkdir -p backups logs
echo -e "${GREEN}✓${NC} Directorios creados"

# Copiar archivo de configuración de ejemplo
echo -e "${YELLOW}[4/5]${NC} Creando archivo de configuración..."
if [ ! -f config.json ]; then
    cp config.example.json config.json
    echo -e "${GREEN}✓${NC} config.json creado desde config.example.json"
    echo -e "${YELLOW}⚠${NC}  Recuerda editar config.json con tus credenciales"
else
    echo -e "${YELLOW}⚠${NC}  config.json ya existe, no se sobrescribió"
fi

# Verificar extensiones PHP
echo -e "${YELLOW}[5/5]${NC} Verificando extensiones PHP..."

check_extension() {
    if php -m | grep -q "^$1$"; then
        echo -e "${GREEN}✓${NC} $1 instalado"
        return 0
    else
        echo -e "${RED}✗${NC} $1 no instalado"
        return 1
    fi
}

check_extension "mysqli"
check_extension "pgsql" || echo -e "   ${YELLOW}→${NC} Opcional para PostgreSQL"
check_extension "mongodb" || echo -e "   ${YELLOW}→${NC} Opcional para MongoDB"
check_extension "sqlite3"

echo ""
echo -e "${GREEN}══════════════════════════════════════════════════════${NC}"
echo -e "${GREEN}✓ Instalación completada!${NC}"
echo -e "${GREEN}══════════════════════════════════════════════════════${NC}"
echo ""
echo "Próximos pasos:"
echo "1. Editar config.json con tus credenciales de base de datos"
echo "2. Probar la conexión: php backup.php test-connection --config=config.json"
echo "3. Realizar tu primer backup: php backup.php backup --config=config.json"
echo ""
echo "Para más información: php backup.php help"
echo ""
