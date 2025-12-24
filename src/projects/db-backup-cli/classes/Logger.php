<?php

/**
 * Logger Class
 * Maneja el registro de todas las actividades del sistema de backup
 */
class Logger {
    private $logFile;
    private $logDir;
    
    public function __construct($logDir = 'logs') {
        $this->logDir = $logDir;
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0755, true);
        }
        $this->logFile = $this->logDir . '/backup_' . date('Y-m-d') . '.log';
    }
    
    /**
     * Registra un mensaje en el archivo de log
     */
    public function log($message, $level = 'INFO') {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
        
        // También mostrar en consola con colores
        $this->printColored($level, $message);
    }
    
    /**
     * Imprime mensajes coloreados en la consola
     */
    private function printColored($level, $message) {
        $colors = [
            'INFO' => "\033[0;32m",    // Verde
            'WARNING' => "\033[0;33m", // Amarillo
            'ERROR' => "\033[0;31m",   // Rojo
            'SUCCESS' => "\033[1;32m", // Verde brillante
            'RESET' => "\033[0m"       // Reset
        ];
        
        $color = $colors[$level] ?? $colors['INFO'];
        echo $color . "[{$level}] " . $colors['RESET'] . $message . PHP_EOL;
    }
    
    /**
     * Registra información de inicio de backup
     */
    public function logBackupStart($dbType, $dbName) {
        $this->log("=== Iniciando backup de {$dbType} - Base de datos: {$dbName} ===", 'INFO');
    }
    
    /**
     * Registra información de finalización de backup
     */
    public function logBackupEnd($success, $duration, $fileSize = null) {
        if ($success) {
            $message = "Backup completado exitosamente en {$duration} segundos";
            if ($fileSize) {
                $message .= " - Tamaño: " . $this->formatBytes($fileSize);
            }
            $this->log($message, 'SUCCESS');
        } else {
            $this->log("Backup fallido después de {$duration} segundos", 'ERROR');
        }
    }
    
    /**
     * Formatea bytes a formato legible
     */
    private function formatBytes($bytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, 2) . ' ' . $units[$pow];
    }
    
    /**
     * Registra un error
     */
    public function error($message) {
        $this->log($message, 'ERROR');
    }
    
    /**
     * Registra una advertencia
     */
    public function warning($message) {
        $this->log($message, 'WARNING');
    }
    
    /**
     * Registra información
     */
    public function info($message) {
        $this->log($message, 'INFO');
    }
    
    /**
     * Registra éxito
     */
    public function success($message) {
        $this->log($message, 'SUCCESS');
    }
}
