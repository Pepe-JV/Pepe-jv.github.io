# Guía de Inicio Rápido - Database Backup CLI Tool

## 🚀 Instalación en 3 Pasos

### 1. Ejecutar el Script de Instalación
```bash
cd src/projects/db-backup-cli
./install.sh
```

### 2. Configurar tu Base de Datos
Edita el archivo `config.json`:
```bash
nano config.json
```

Ejemplo para MySQL:
```json
{
  "database": {
    "type": "mysql",
    "host": "localhost",
    "port": 3306,
    "username": "tu_usuario",
    "password": "tu_contraseña",
    "database": "nombre_de_tu_base_de_datos"
  }
}
```

### 3. Probar la Conexión
```bash
php backup.php test-connection --config=config.json
```

## 📝 Comandos Básicos

### Realizar un Backup
```bash
# Backup simple
php backup.php backup --config=config.json

# Backup con todas las opciones
php backup.php backup --config=config.json --type=full --cloud --notify
```

### Listar Backups Existentes
```bash
php backup.php list-backups --config=config.json
```

### Restaurar una Base de Datos
```bash
php backup.php restore --config=config.json --file=backups/midb_full_2024-12-24_15-30-00.sql.gz
```

### Ver Ayuda
```bash
php backup.php help
```

## 🧪 Prueba Rápida con SQLite

Si no tienes una base de datos configurada, puedes probar con SQLite:

1. Crear una base de datos de prueba:
```bash
cd src/projects/db-backup-cli
sqlite3 test.db "CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, email TEXT);"
sqlite3 test.db "INSERT INTO users (name, email) VALUES ('Jose Justicia', 'test@example.com');"
```

2. Configurar para SQLite en `config.json`:
```json
{
  "database": {
    "type": "sqlite",
    "database": "test.db"
  },
  "backup": {
    "directory": "backups",
    "compress": true
  }
}
```

3. Realizar backup:
```bash
php backup.php backup --config=config.json
```

4. Ver los backups creados:
```bash
ls -lh backups/
```

## 📦 Estructura de Archivos Creados

```
db-backup-cli/
├── backup.php           # ← Script principal (ejecutable)
├── install.sh          # ← Script de instalación
├── config.json         # ← Tu configuración (editar)
├── config.example.json # ← Ejemplo de configuración
├── README.md           # ← Documentación completa
├── .gitignore         # ← Archivos ignorados por git
├── classes/           # ← Clases del sistema
│   ├── BackupManager.php
│   ├── CloudStorage.php
│   ├── DatabaseConnector.php
│   ├── Logger.php
│   └── Notifier.php
├── backups/           # ← Backups se guardan aquí
└── logs/             # ← Logs se guardan aquí
```

## ⚙️ Configuración Avanzada

### Habilitar Cloud Storage (AWS S3)

1. Instalar AWS CLI:
```bash
sudo apt-get install awscli
aws configure
```

2. Configurar en `config.json`:
```json
{
  "cloud_storage": {
    "enabled": true,
    "provider": "s3",
    "bucket": "mi-bucket-backups",
    "region": "us-east-1"
  }
}
```

3. Hacer backup con upload a cloud:
```bash
php backup.php backup --config=config.json --cloud
```

### Habilitar Notificaciones Slack

1. Crear un Webhook de Slack:
   - Ir a https://api.slack.com/messaging/webhooks
   - Crear un nuevo Webhook
   - Copiar la URL

2. Configurar en `config.json`:
```json
{
  "notifications": {
    "enabled": true,
    "type": "slack",
    "webhook_url": "https://hooks.slack.com/services/TU/WEBHOOK/URL"
  }
}
```

3. Hacer backup con notificación:
```bash
php backup.php backup --config=config.json --notify
```

## 🔄 Automatización con Cron

Para backups automáticos diarios a las 2 AM:

```bash
# Editar crontab
crontab -e

# Añadir esta línea
0 2 * * * cd /ruta/completa/al/proyecto && php backup.php backup --config=config.json --cloud --notify
```

## 🐛 Solución de Problemas

### Error: "Permission denied"
```bash
chmod +x backup.php install.sh
chmod 755 backups logs
```

### Error: "mysqldump: command not found"
```bash
# Ubuntu/Debian
sudo apt-get install mysql-client

# macOS
brew install mysql-client
```

### Error: "No se pudo conectar"
- Verificar que el servidor de base de datos esté corriendo
- Comprobar credenciales en config.json
- Probar conexión manual: `mysql -h localhost -u root -p`

## 📚 Recursos

- **Documentación completa**: Ver `README.md`
- **Página del proyecto**: Abrir `index.html` en el navegador
- **GitHub**: [https://github.com/Pepe-JV](https://github.com/Pepe-JV)

## 💡 Ejemplos de Uso Real

### Backup antes de actualización
```bash
php backup.php backup --config=config.json --type=full --cloud --notify
# ... realizar actualización ...
```

### Restaurar después de error
```bash
php backup.php list-backups --config=config.json
php backup.php restore --config=config.json --file=backups/ultimo_backup.sql.gz
```

### Migrar entre servidores
```bash
# Servidor origen
php backup.php backup --config=config.json --cloud

# Servidor destino (descargar desde S3 y restaurar)
aws s3 cp s3://mi-bucket/backup.sql.gz ./backups/
php backup.php restore --config=config.json --file=backups/backup.sql.gz
```

---

**¡Listo para usar!** 🎉

Para más detalles, consulta la documentación completa en `README.md`
