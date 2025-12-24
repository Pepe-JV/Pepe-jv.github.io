# Database Backup CLI Tool 🗄️

Una herramienta de línea de comandos (CLI) profesional y completa para realizar copias de seguridad de bases de datos. Soporta múltiples sistemas de gestión de bases de datos (MySQL, PostgreSQL, MongoDB, SQLite), compresión de archivos, almacenamiento en la nube y notificaciones automáticas.

## 🚀 Características

### Conectividad de Bases de Datos
- ✅ **MySQL** - Soporte completo con mysqldump
- ✅ **PostgreSQL** - Soporte completo con pg_dump
- ✅ **MongoDB** - Soporte completo con mongodump
- ✅ **SQLite** - Copia directa de archivos
- 🔐 Validación de credenciales antes de operaciones
- ⚡ Prueba de conexión rápida

### Operaciones de Backup
- 📦 **Tipos de backup**: Full, Incremental, Differential
- 🗜️ **Compresión automática** con gzip para reducir espacio
- ⏱️ Registro de tiempo de ejecución
- 📊 Estadísticas de tamaño de archivo

### Almacenamiento
- 💾 **Local**: Almacenamiento en sistema de archivos local
- ☁️ **Cloud Storage**: 
  - AWS S3
  - Google Cloud Storage (GCS)
  - Azure Blob Storage
- 📋 Listado de backups locales y en la nube

### Logging y Notificaciones
- 📝 Sistema de logging completo con niveles (INFO, WARNING, ERROR, SUCCESS)
- 🎨 Salida colorizada en consola
- 💬 **Notificaciones Slack** con mensajes formateados
- 📧 **Notificaciones por email** (opcional)
- ⏲️ Registro de duración de operaciones

### Operaciones de Restauración
- ♻️ Restauración completa de bases de datos
- 🔓 Descompresión automática de archivos
- 🎯 Soporte para restauración selectiva (según DBMS)

## 📋 Requisitos

### Requisitos del Sistema
- PHP 7.4 o superior
- Acceso a línea de comandos
- Permisos de lectura/escritura en directorios de trabajo

### Herramientas de Línea de Comandos
Dependiendo del tipo de base de datos que uses:

#### MySQL
```bash
# Linux/Ubuntu
sudo apt-get install mysql-client

# macOS
brew install mysql-client

# Windows
# Descargar desde https://dev.mysql.com/downloads/mysql/
```

#### PostgreSQL
```bash
# Linux/Ubuntu
sudo apt-get install postgresql-client

# macOS
brew install postgresql

# Windows
# Descargar desde https://www.postgresql.org/download/windows/
```

#### MongoDB
```bash
# Linux/Ubuntu
sudo apt-get install mongodb-database-tools

# macOS
brew install mongodb-database-tools

# Windows
# Descargar desde https://www.mongodb.com/try/download/database-tools
```

#### Almacenamiento Cloud (Opcional)
```bash
# AWS CLI para S3
sudo apt-get install awscli
aws configure

# Google Cloud SDK para GCS
# Seguir: https://cloud.google.com/sdk/docs/install

# Azure CLI para Blob Storage
curl -sL https://aka.ms/InstallAzureCLIDeb | sudo bash
az login
```

### Extensiones PHP
```bash
# MySQL
sudo apt-get install php-mysqli

# PostgreSQL
sudo apt-get install php-pgsql

# MongoDB
sudo pecl install mongodb

# SQLite (generalmente incluido)
sudo apt-get install php-sqlite3
```

## 🔧 Instalación

1. **Clonar o descargar el proyecto**
```bash
git clone https://github.com/tu-usuario/db-backup-cli.git
cd db-backup-cli
```

2. **Dar permisos de ejecución al script**
```bash
chmod +x backup.php
```

3. **Copiar el archivo de configuración de ejemplo**
```bash
cp config.example.json config.json
```

4. **Editar la configuración**
```bash
nano config.json
```

## ⚙️ Configuración

Edita el archivo `config.json` con tus credenciales y preferencias:

```json
{
  "database": {
    "type": "mysql",
    "host": "localhost",
    "port": 3306,
    "username": "root",
    "password": "tu_contraseña",
    "database": "nombre_base_datos"
  },
  "backup": {
    "directory": "backups",
    "compress": true,
    "type": "full"
  },
  "cloud_storage": {
    "enabled": false,
    "provider": "s3",
    "bucket": "mi-bucket-backups",
    "region": "us-east-1"
  },
  "notifications": {
    "enabled": false,
    "type": "slack",
    "webhook_url": "https://hooks.slack.com/services/YOUR/WEBHOOK/URL"
  },
  "logging": {
    "directory": "logs",
    "level": "info"
  }
}
```

### Tipos de Base de Datos Soportados
- `mysql` - MySQL/MariaDB
- `postgresql` o `postgres` - PostgreSQL
- `mongodb` - MongoDB
- `sqlite` - SQLite

### Proveedores de Cloud Storage
- `s3` o `aws` - Amazon S3
- `gcs` o `google` - Google Cloud Storage
- `azure` - Azure Blob Storage

## 📖 Uso

### Comando de Ayuda
```bash
php backup.php help
```

### Probar Conexión
```bash
php backup.php test-connection --config=config.json
```

### Realizar Backup

**Backup básico:**
```bash
php backup.php backup --config=config.json
```

**Backup con todas las opciones:**
```bash
php backup.php backup --config=config.json --type=full --cloud --notify
```

**Backup sin compresión:**
```bash
php backup.php backup --config=config.json --no-compress
```

### Restaurar Base de Datos

```bash
php backup.php restore --config=config.json --file=backups/midb_full_2024-12-24_15-30-00.sql.gz
```

**Con notificación:**
```bash
php backup.php restore --config=config.json --file=backups/backup.sql.gz --notify
```

### Listar Backups

```bash
php backup.php list-backups --config=config.json
```

## 📊 Ejemplos de Salida

### Backup Exitoso
```
╔═══════════════════════════════════════════════════════╗
║     DATABASE BACKUP CLI TOOL v1.0.0                  ║
║     Herramienta de Copias de Seguridad de BBDD      ║
║     Por Jose Justicia                                ║
╚═══════════════════════════════════════════════════════╝

[SUCCESS] Conexión MySQL exitosa a localhost:3306/mibasedatos
[INFO] === Iniciando backup de mysql - Base de datos: mibasedatos ===
[INFO] Ejecutando mysqldump...
[INFO] Comprimiendo backup...
[SUCCESS] Backup comprimido exitosamente
[SUCCESS] Backup completado exitosamente en 2.34 segundos - Tamaño: 15.67 MB
```

### Notificación Slack
La herramienta envía notificaciones ricas en Slack con:
- Estado del backup (✅ Éxito / ❌ Fallo)
- Nombre y tipo de base de datos
- Duración de la operación
- Tamaño del archivo generado
- Timestamp
- Mensajes de error (si aplica)

## 🔒 Seguridad

### Mejores Prácticas
1. **No commitear archivos de configuración** con credenciales reales
2. **Usar variables de entorno** para datos sensibles
3. **Cifrar backups** antes de subir a la nube
4. **Rotar credenciales** regularmente
5. **Limitar permisos** de archivos de configuración:
   ```bash
   chmod 600 config.json
   ```

### Protección de Contraseñas
Considera usar variables de entorno:

```bash
export DB_PASSWORD="tu_contraseña_segura"
```

Y modificar el código para leerlas:
```php
$config['database']['password'] = getenv('DB_PASSWORD');
```

## 🔄 Programación Automática

### Usando Cron (Linux/macOS)

Editar crontab:
```bash
crontab -e
```

**Backup diario a las 2 AM:**
```cron
0 2 * * * cd /ruta/al/proyecto && php backup.php backup --config=config.json --cloud --notify
```

**Backup cada 6 horas:**
```cron
0 */6 * * * cd /ruta/al/proyecto && php backup.php backup --config=config.json
```

**Backup semanal los domingos a las 3 AM:**
```cron
0 3 * * 0 cd /ruta/al/proyecto && php backup.php backup --config=config.json --cloud --notify
```

### Usando Task Scheduler (Windows)

1. Abrir "Programador de tareas"
2. Crear tarea básica
3. Configurar trigger (diario, semanal, etc.)
4. Acción: Iniciar un programa
   - Programa: `php.exe`
   - Argumentos: `C:\ruta\backup.php backup --config=config.json`

## 📁 Estructura del Proyecto

```
db-backup-cli/
├── backup.php              # Script principal CLI
├── config.example.json     # Configuración de ejemplo
├── config.json            # Tu configuración (no versionar)
├── README.md              # Esta documentación
├── classes/               # Clases del sistema
│   ├── Logger.php         # Sistema de logging
│   ├── DatabaseConnector.php  # Conexiones a BBDD
│   ├── BackupManager.php  # Gestión de backups
│   ├── CloudStorage.php   # Integración cloud
│   └── Notifier.php       # Sistema de notificaciones
├── backups/              # Backups locales (crear automáticamente)
└── logs/                 # Archivos de log (crear automáticamente)
```

## 🐛 Solución de Problemas

### Error: "mysqldump: command not found"
```bash
# Instalar herramientas de MySQL
sudo apt-get install mysql-client
```

### Error: "Permission denied"
```bash
# Dar permisos de ejecución
chmod +x backup.php

# Verificar permisos de directorios
chmod 755 backups logs
```

### Error: "No se pudo conectar a la base de datos"
1. Verificar credenciales en `config.json`
2. Comprobar que el servidor de BBDD está corriendo
3. Verificar configuración de firewall
4. Probar conexión manualmente:
   ```bash
   mysql -h localhost -u root -p
   ```

### Error: "Failed to upload to S3"
1. Verificar configuración de AWS CLI: `aws configure`
2. Comprobar permisos del bucket
3. Verificar conectividad a internet

## 🎯 Casos de Uso

### 1. Backup Automático Nocturno
```bash
# Crontab: Backup diario con upload a S3 y notificación
0 2 * * * cd /var/www/backup-tool && php backup.php backup --config=config.json --cloud --notify
```

### 2. Backup Antes de Actualización
```bash
# Script de deployment
php backup.php backup --config=config.json --type=full --notify
# ... continuar con deployment
```

### 3. Disaster Recovery
```bash
# Descargar backup más reciente de S3 y restaurar
php backup.php restore --config=config.json --file=latest_backup.sql.gz --notify
```

### 4. Migración de Servidor
```bash
# Servidor origen
php backup.php backup --config=config.json --cloud

# Servidor destino
# Descargar desde S3 y restaurar
php backup.php restore --config=config.json --file=backup.sql.gz
```

## 📈 Mejoras Futuras

- [ ] Soporte para backup incremental real (no solo nomenclatura)
- [ ] Encriptación de backups (AES-256)
- [ ] Interfaz web de administración
- [ ] Retención automática de backups (eliminar antiguos)
- [ ] Backup de múltiples bases de datos en paralelo
- [ ] Verificación de integridad de backups
- [ ] Soporte para más DBMS (Oracle, SQL Server)
- [ ] Estadísticas y dashboards
- [ ] API REST

## 👨‍💻 Autor

**Jose Justicia**
- GitHub: [@Pepe-JV](https://github.com/Pepe-JV)
- LinkedIn: [Jose Justicia](https://www.linkedin.com/in/jose-justicia-vico-1a4785276/)

## 📝 Licencia

Este proyecto es de código abierto y está disponible bajo la [Licencia MIT](LICENSE).

## 🤝 Contribuciones

Las contribuciones son bienvenidas! Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 🙏 Agradecimientos

- A la comunidad de PHP por las excelentes herramientas
- A los desarrolladores de las herramientas CLI de bases de datos
- A todos los que contribuyan al proyecto

---

**⭐ Si este proyecto te resulta útil, considera darle una estrella en GitHub!**
