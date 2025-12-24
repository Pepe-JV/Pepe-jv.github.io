#!/usr/bin/env php
<?php
/**
 * Database Backup CLI Tool
 * Herramienta de línea de comandos para realizar copias de seguridad de bases de datos
 * 
 * @author Jose Justicia
 * @version 1.0.0
 */

// Cargar las clases
require_once __DIR__ . '/classes/Logger.php';
require_once __DIR__ . '/classes/DatabaseConnector.php';
require_once __DIR__ . '/classes/BackupManager.php';
require_once __DIR__ . '/classes/CloudStorage.php';
require_once __DIR__ . '/classes/Notifier.php';

// Banner de la aplicación
function showBanner() {
    echo "\033[1;36m";
    echo "╔═══════════════════════════════════════════════════════╗\n";
    echo "║     DATABASE BACKUP CLI TOOL v1.0.0                  ║\n";
    echo "║     Herramienta de Copias de Seguridad de BBDD      ║\n";
    echo "║     Por Jose Justicia                                ║\n";
    echo "╚═══════════════════════════════════════════════════════╝\n";
    echo "\033[0m\n";
}

// Muestra la ayuda
function showHelp() {
    showBanner();
    echo "Uso: php backup.php [comando] [opciones]\n\n";
    
    echo "\033[1;33mComandos disponibles:\033[0m\n";
    echo "  backup              Realiza una copia de seguridad de la base de datos\n";
    echo "  restore             Restaura una base de datos desde un archivo de backup\n";
    echo "  test-connection     Prueba la conexión a la base de datos\n";
    echo "  list-backups        Lista los backups disponibles\n";
    echo "  help                Muestra esta ayuda\n\n";
    
    echo "\033[1;33mOpciones para 'backup':\033[0m\n";
    echo "  --config=FILE       Archivo de configuración JSON (requerido)\n";
    echo "  --type=TYPE         Tipo de backup: full, incremental, differential (default: full)\n";
    echo "  --compress          Comprimir el archivo de backup (default: true)\n";
    echo "  --no-compress       No comprimir el archivo de backup\n";
    echo "  --cloud             Subir el backup al almacenamiento cloud\n";
    echo "  --notify            Enviar notificación al completar\n\n";
    
    echo "\033[1;33mOpciones para 'restore':\033[0m\n";
    echo "  --config=FILE       Archivo de configuración JSON (requerido)\n";
    echo "  --file=FILE         Archivo de backup a restaurar (requerido)\n";
    echo "  --notify            Enviar notificación al completar\n\n";
    
    echo "\033[1;33mOpciones para 'test-connection':\033[0m\n";
    echo "  --config=FILE       Archivo de configuración JSON (requerido)\n\n";
    
    echo "\033[1;33mEjemplos:\033[0m\n";
    echo "  php backup.php backup --config=config.json\n";
    echo "  php backup.php backup --config=config.json --type=full --cloud --notify\n";
    echo "  php backup.php restore --config=config.json --file=backup.sql.gz\n";
    echo "  php backup.php test-connection --config=config.json\n";
    echo "  php backup.php list-backups --config=config.json\n\n";
}

// Parsea los argumentos de línea de comandos
function parseArguments($argv) {
    $args = [
        'command' => null,
        'options' => []
    ];
    
    // El primer argumento después del script es el comando
    if (isset($argv[1]) && substr($argv[1], 0, 2) !== '--') {
        $args['command'] = $argv[1];
        array_shift($argv); // Remover el nombre del script
        array_shift($argv); // Remover el comando
    } else {
        array_shift($argv); // Remover solo el nombre del script
    }
    
    // Parsear las opciones
    foreach ($argv as $arg) {
        if (substr($arg, 0, 2) === '--') {
            $arg = substr($arg, 2);
            if (strpos($arg, '=') !== false) {
                list($key, $value) = explode('=', $arg, 2);
                $args['options'][$key] = $value;
            } else {
                $args['options'][$arg] = true;
            }
        }
    }
    
    return $args;
}

// Carga la configuración desde un archivo JSON
function loadConfig($configFile) {
    if (!file_exists($configFile)) {
        throw new Exception("Archivo de configuración no encontrado: {$configFile}");
    }
    
    $content = file_get_contents($configFile);
    $config = json_decode($content, true);
    
    if ($config === null) {
        throw new Exception("Error al parsear el archivo de configuración JSON");
    }
    
    return $config;
}

// Comando: backup
function commandBackup($options, $config, Logger $logger) {
    $dbConfig = $config['database'];
    $backupType = $options['type'] ?? 'full';
    $compress = !isset($options['no-compress']);
    $uploadCloud = isset($options['cloud']);
    $sendNotification = isset($options['notify']);
    
    // Crear el conector de base de datos
    $connector = new DatabaseConnector($dbConfig['type'], $dbConfig, $logger);
    
    // Probar conexión
    if (!$connector->testConnection()) {
        $logger->error("No se pudo conectar a la base de datos");
        return false;
    }
    
    // Crear el gestor de backups
    $backupDir = $config['backup']['directory'] ?? 'backups';
    $backupManager = new BackupManager($connector, $logger, $backupDir, $compress);
    
    // Realizar el backup
    $result = $backupManager->backup($backupType);
    
    if (!$result['success']) {
        if ($sendNotification && isset($config['notifications'])) {
            $notifier = new Notifier($config['notifications'], $logger);
            $notifier->notifyBackup($result, $dbConfig['database'], $dbConfig['type']);
        }
        return false;
    }
    
    // Subir a cloud si está habilitado
    if ($uploadCloud && isset($config['cloud_storage']['enabled']) && $config['cloud_storage']['enabled']) {
        $cloudConfig = $config['cloud_storage'];
        $cloudStorage = new CloudStorage($cloudConfig['provider'], $cloudConfig, $logger);
        
        $uploaded = $cloudStorage->upload($result['filepath']);
        $result['cloud_uploaded'] = $uploaded;
    }
    
    // Enviar notificación si está habilitado
    if ($sendNotification && isset($config['notifications'])) {
        $notifier = new Notifier($config['notifications'], $logger);
        $notifier->notifyBackup($result, $dbConfig['database'], $dbConfig['type']);
    }
    
    return true;
}

// Comando: restore
function commandRestore($options, $config, Logger $logger) {
    if (!isset($options['file'])) {
        $logger->error("Debe especificar el archivo de backup con --file=FILE");
        return false;
    }
    
    $backupFile = $options['file'];
    $sendNotification = isset($options['notify']);
    $dbConfig = $config['database'];
    
    // Crear el conector de base de datos
    $connector = new DatabaseConnector($dbConfig['type'], $dbConfig, $logger);
    
    // Probar conexión
    if (!$connector->testConnection()) {
        $logger->error("No se pudo conectar a la base de datos");
        return false;
    }
    
    // Crear el gestor de backups
    $backupDir = $config['backup']['directory'] ?? 'backups';
    $backupManager = new BackupManager($connector, $logger, $backupDir);
    
    // Realizar la restauración
    $result = $backupManager->restore($backupFile);
    
    // Enviar notificación si está habilitado
    if ($sendNotification && isset($config['notifications'])) {
        $notifier = new Notifier($config['notifications'], $logger);
        $notifier->notifyRestore($result, $dbConfig['database'], $dbConfig['type']);
    }
    
    return $result['success'];
}

// Comando: test-connection
function commandTestConnection($options, $config, Logger $logger) {
    $dbConfig = $config['database'];
    
    $logger->info("Probando conexión a la base de datos...");
    $logger->info("Tipo: {$dbConfig['type']}");
    $logger->info("Base de datos: {$dbConfig['database']}");
    
    $connector = new DatabaseConnector($dbConfig['type'], $dbConfig, $logger);
    
    if ($connector->testConnection()) {
        $logger->success("✓ Conexión exitosa!");
        return true;
    } else {
        $logger->error("✗ Fallo en la conexión");
        return false;
    }
}

// Comando: list-backups
function commandListBackups($options, $config, Logger $logger) {
    $backupDir = $config['backup']['directory'] ?? 'backups';
    
    if (!is_dir($backupDir)) {
        $logger->warning("Directorio de backups no encontrado: {$backupDir}");
        return false;
    }
    
    $logger->info("Listando backups en: {$backupDir}");
    echo "\n";
    
    $files = glob($backupDir . '/*');
    
    if (empty($files)) {
        $logger->warning("No se encontraron archivos de backup");
        return true;
    }
    
    // Ordenar por fecha de modificación (más reciente primero)
    usort($files, function($a, $b) {
        return filemtime($b) - filemtime($a);
    });
    
    echo "\033[1;36m";
    printf("%-50s %-15s %-20s\n", "Archivo", "Tamaño", "Fecha");
    echo str_repeat("-", 85) . "\n";
    echo "\033[0m";
    
    foreach ($files as $file) {
        $filename = basename($file);
        $size = filesize($file);
        $date = date('Y-m-d H:i:s', filemtime($file));
        
        $sizeFormatted = formatBytes($size);
        
        printf("%-50s %-15s %-20s\n", $filename, $sizeFormatted, $date);
    }
    
    echo "\n";
    $logger->info("Total de backups: " . count($files));
    
    // Si el cloud storage está habilitado, también listar desde ahí
    if (isset($config['cloud_storage']['enabled']) && $config['cloud_storage']['enabled']) {
        echo "\n";
        $logger->info("Backups en almacenamiento cloud:");
        
        $cloudConfig = $config['cloud_storage'];
        $cloudStorage = new CloudStorage($cloudConfig['provider'], $cloudConfig, $logger);
        $cloudBackups = $cloudStorage->listBackups();
        
        if (!empty($cloudBackups)) {
            echo "\033[1;36m";
            foreach ($cloudBackups as $backup) {
                echo "  " . $backup . "\n";
            }
            echo "\033[0m";
        }
    }
    
    return true;
}

// Función auxiliar para formatear bytes
function formatBytes($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}

// Función principal
function main($argv) {
    // Parsear argumentos
    $args = parseArguments($argv);
    $command = $args['command'];
    $options = $args['options'];
    
    // Si no hay comando o es help, mostrar ayuda
    if ($command === null || $command === 'help') {
        showHelp();
        return 0;
    }
    
    // Verificar que se proporcionó archivo de configuración
    if (!isset($options['config'])) {
        echo "\033[0;31m[ERROR]\033[0m Debe especificar un archivo de configuración con --config=FILE\n";
        echo "Use 'php backup.php help' para más información\n";
        return 1;
    }
    
    try {
        // Cargar configuración
        $config = loadConfig($options['config']);
        
        // Crear logger
        $logDir = $config['logging']['directory'] ?? 'logs';
        $logger = new Logger($logDir);
        
        // Ejecutar comando
        $success = false;
        
        switch ($command) {
            case 'backup':
                showBanner();
                $success = commandBackup($options, $config, $logger);
                break;
                
            case 'restore':
                showBanner();
                $success = commandRestore($options, $config, $logger);
                break;
                
            case 'test-connection':
                showBanner();
                $success = commandTestConnection($options, $config, $logger);
                break;
                
            case 'list-backups':
                showBanner();
                $success = commandListBackups($options, $config, $logger);
                break;
                
            default:
                echo "\033[0;31m[ERROR]\033[0m Comando desconocido: {$command}\n";
                echo "Use 'php backup.php help' para ver los comandos disponibles\n";
                return 1;
        }
        
        return $success ? 0 : 1;
        
    } catch (Exception $e) {
        echo "\033[0;31m[ERROR]\033[0m " . $e->getMessage() . "\n";
        return 1;
    }
}

// Ejecutar la aplicación
exit(main($argv));
