<?php

/**
 * BackupManager Class
 * Gestiona las operaciones de backup y restauración de bases de datos
 */
class BackupManager {
    private $connector;
    private $logger;
    private $backupDir;
    private $compress;
    
    public function __construct(DatabaseConnector $connector, Logger $logger, $backupDir = 'backups', $compress = true) {
        $this->connector = $connector;
        $this->logger = $logger;
        $this->backupDir = $backupDir;
        $this->compress = $compress;
        
        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }
    
    /**
     * Realiza el backup de la base de datos
     */
    public function backup($backupType = 'full') {
        $startTime = microtime(true);
        $config = $this->connector->getConfig();
        $dbType = $this->connector->getDbType();
        $dbName = $config['database'];
        
        $this->logger->logBackupStart($dbType, $dbName);
        
        try {
            $filename = $this->generateFilename($dbType, $dbName, $backupType);
            $filepath = $this->backupDir . '/' . $filename;
            
            switch ($dbType) {
                case 'mysql':
                    $success = $this->backupMySQL($filepath, $config);
                    break;
                case 'postgresql':
                case 'postgres':
                    $success = $this->backupPostgreSQL($filepath, $config);
                    break;
                case 'mongodb':
                    $success = $this->backupMongoDB($filepath, $config);
                    break;
                case 'sqlite':
                    $success = $this->backupSQLite($filepath, $config);
                    break;
                default:
                    throw new Exception("Tipo de base de datos no soportado: {$dbType}");
            }
            
            if ($success && $this->compress) {
                $this->compressBackup($filepath);
                $filepath .= '.gz';
            }
            
            $duration = round(microtime(true) - $startTime, 2);
            $fileSize = file_exists($filepath) ? filesize($filepath) : 0;
            
            $this->logger->logBackupEnd($success, $duration, $fileSize);
            
            return [
                'success' => $success,
                'filepath' => $filepath,
                'duration' => $duration,
                'filesize' => $fileSize
            ];
            
        } catch (Exception $e) {
            $duration = round(microtime(true) - $startTime, 2);
            $this->logger->error("Error durante el backup: " . $e->getMessage());
            $this->logger->logBackupEnd(false, $duration);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Genera el nombre del archivo de backup
     */
    private function generateFilename($dbType, $dbName, $backupType) {
        $timestamp = date('Y-m-d_H-i-s');
        $extension = $this->getFileExtension($dbType);
        return "{$dbName}_{$backupType}_{$timestamp}.{$extension}";
    }
    
    /**
     * Obtiene la extensión del archivo según el tipo de BD
     */
    private function getFileExtension($dbType) {
        $extensions = [
            'mysql' => 'sql',
            'postgresql' => 'sql',
            'postgres' => 'sql',
            'mongodb' => 'archive',
            'sqlite' => 'db'
        ];
        return $extensions[$dbType] ?? 'bak';
    }
    
    /**
     * Realiza backup de MySQL
     */
    private function backupMySQL($filepath, $config) {
        $host = $config['host'] ?? 'localhost';
        $port = $config['port'] ?? 3306;
        $user = $config['username'];
        $pass = $config['password'];
        $db = $config['database'];
        
        $command = sprintf(
            'mysqldump -h %s -P %s -u %s -p%s %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($user),
            escapeshellarg($pass),
            escapeshellarg($db),
            escapeshellarg($filepath)
        );
        
        $this->logger->info("Ejecutando mysqldump...");
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new Exception("mysqldump falló: " . implode("\n", $output));
        }
        
        return file_exists($filepath) && filesize($filepath) > 0;
    }
    
    /**
     * Realiza backup de PostgreSQL
     */
    private function backupPostgreSQL($filepath, $config) {
        $host = $config['host'] ?? 'localhost';
        $port = $config['port'] ?? 5432;
        $user = $config['username'];
        $db = $config['database'];
        
        // Configurar variable de entorno para la contraseña
        putenv("PGPASSWORD=" . $config['password']);
        
        $command = sprintf(
            'pg_dump -h %s -p %s -U %s -F c -b -v -f %s %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($user),
            escapeshellarg($filepath),
            escapeshellarg($db)
        );
        
        $this->logger->info("Ejecutando pg_dump...");
        exec($command, $output, $returnCode);
        
        putenv("PGPASSWORD");
        
        if ($returnCode !== 0) {
            throw new Exception("pg_dump falló: " . implode("\n", $output));
        }
        
        return file_exists($filepath) && filesize($filepath) > 0;
    }
    
    /**
     * Realiza backup de MongoDB
     */
    private function backupMongoDB($filepath, $config) {
        $host = $config['host'] ?? 'localhost';
        $port = $config['port'] ?? 27017;
        $user = $config['username'] ?? '';
        $pass = $config['password'] ?? '';
        $db = $config['database'];
        
        $auth = ($user && $pass) ? "-u " . escapeshellarg($user) . " -p " . escapeshellarg($pass) : "";
        
        $command = sprintf(
            'mongodump --host %s --port %s %s --db %s --archive=%s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            $auth,
            escapeshellarg($db),
            escapeshellarg($filepath)
        );
        
        $this->logger->info("Ejecutando mongodump...");
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new Exception("mongodump falló: " . implode("\n", $output));
        }
        
        return file_exists($filepath) && filesize($filepath) > 0;
    }
    
    /**
     * Realiza backup de SQLite
     */
    private function backupSQLite($filepath, $config) {
        $sourcePath = $config['database'];
        
        $this->logger->info("Copiando archivo SQLite...");
        
        if (!copy($sourcePath, $filepath)) {
            throw new Exception("No se pudo copiar el archivo SQLite");
        }
        
        return file_exists($filepath) && filesize($filepath) > 0;
    }
    
    /**
     * Comprime el archivo de backup usando gzip
     */
    private function compressBackup($filepath) {
        if (!file_exists($filepath)) {
            throw new Exception("Archivo de backup no encontrado para comprimir");
        }
        
        $this->logger->info("Comprimiendo backup...");
        
        $command = sprintf('gzip -9 %s 2>&1', escapeshellarg($filepath));
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new Exception("Error al comprimir: " . implode("\n", $output));
        }
        
        $this->logger->success("Backup comprimido exitosamente");
    }
    
    /**
     * Restaura una base de datos desde un archivo de backup
     */
    public function restore($backupFile) {
        $startTime = microtime(true);
        
        if (!file_exists($backupFile)) {
            $this->logger->error("Archivo de backup no encontrado: {$backupFile}");
            return ['success' => false, 'error' => 'Archivo no encontrado'];
        }
        
        $this->logger->info("Iniciando restauración desde: {$backupFile}");
        
        try {
            $config = $this->connector->getConfig();
            $dbType = $this->connector->getDbType();
            
            // Descomprimir si es necesario
            if (substr($backupFile, -3) === '.gz') {
                $this->logger->info("Descomprimiendo backup...");
                $command = sprintf('gunzip -k %s 2>&1', escapeshellarg($backupFile));
                exec($command, $output, $returnCode);
                $backupFile = substr($backupFile, 0, -3);
            }
            
            switch ($dbType) {
                case 'mysql':
                    $success = $this->restoreMySQL($backupFile, $config);
                    break;
                case 'postgresql':
                case 'postgres':
                    $success = $this->restorePostgreSQL($backupFile, $config);
                    break;
                case 'mongodb':
                    $success = $this->restoreMongoDB($backupFile, $config);
                    break;
                case 'sqlite':
                    $success = $this->restoreSQLite($backupFile, $config);
                    break;
                default:
                    throw new Exception("Tipo de base de datos no soportado: {$dbType}");
            }
            
            $duration = round(microtime(true) - $startTime, 2);
            
            if ($success) {
                $this->logger->success("Restauración completada en {$duration} segundos");
            } else {
                $this->logger->error("Restauración fallida después de {$duration} segundos");
            }
            
            return ['success' => $success, 'duration' => $duration];
            
        } catch (Exception $e) {
            $duration = round(microtime(true) - $startTime, 2);
            $this->logger->error("Error durante la restauración: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Restaura MySQL
     */
    private function restoreMySQL($backupFile, $config) {
        $host = $config['host'] ?? 'localhost';
        $port = $config['port'] ?? 3306;
        $user = $config['username'];
        $pass = $config['password'];
        $db = $config['database'];
        
        $command = sprintf(
            'mysql -h %s -P %s -u %s -p%s %s < %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($user),
            escapeshellarg($pass),
            escapeshellarg($db),
            escapeshellarg($backupFile)
        );
        
        exec($command, $output, $returnCode);
        return $returnCode === 0;
    }
    
    /**
     * Restaura PostgreSQL
     */
    private function restorePostgreSQL($backupFile, $config) {
        $host = $config['host'] ?? 'localhost';
        $port = $config['port'] ?? 5432;
        $user = $config['username'];
        $db = $config['database'];
        
        putenv("PGPASSWORD=" . $config['password']);
        
        $command = sprintf(
            'pg_restore -h %s -p %s -U %s -d %s -c %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($user),
            escapeshellarg($db),
            escapeshellarg($backupFile)
        );
        
        exec($command, $output, $returnCode);
        putenv("PGPASSWORD");
        
        return $returnCode === 0;
    }
    
    /**
     * Restaura MongoDB
     */
    private function restoreMongoDB($backupFile, $config) {
        $host = $config['host'] ?? 'localhost';
        $port = $config['port'] ?? 27017;
        $user = $config['username'] ?? '';
        $pass = $config['password'] ?? '';
        $db = $config['database'];
        
        $auth = ($user && $pass) ? "-u " . escapeshellarg($user) . " -p " . escapeshellarg($pass) : "";
        
        $command = sprintf(
            'mongorestore --host %s --port %s %s --db %s --archive=%s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            $auth,
            escapeshellarg($db),
            escapeshellarg($backupFile)
        );
        
        exec($command, $output, $returnCode);
        return $returnCode === 0;
    }
    
    /**
     * Restaura SQLite
     */
    private function restoreSQLite($backupFile, $config) {
        $targetPath = $config['database'];
        return copy($backupFile, $targetPath);
    }
}
