<?php

/**
 * DatabaseConnector Class
 * Maneja las conexiones a diferentes tipos de bases de datos
 */
class DatabaseConnector {
    private $dbType;
    private $config;
    private $connection;
    private $logger;
    
    public function __construct($dbType, $config, Logger $logger) {
        $this->dbType = strtolower($dbType);
        $this->config = $config;
        $this->logger = $logger;
    }
    
    /**
     * Prueba la conexión a la base de datos
     */
    public function testConnection() {
        try {
            switch ($this->dbType) {
                case 'mysql':
                    return $this->testMySQLConnection();
                case 'postgresql':
                case 'postgres':
                    return $this->testPostgreSQLConnection();
                case 'mongodb':
                    return $this->testMongoDBConnection();
                case 'sqlite':
                    return $this->testSQLiteConnection();
                default:
                    throw new Exception("Tipo de base de datos no soportado: {$this->dbType}");
            }
        } catch (Exception $e) {
            $this->logger->error("Error al conectar: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Prueba conexión MySQL
     */
    private function testMySQLConnection() {
        $host = $this->config['host'] ?? 'localhost';
        $port = $this->config['port'] ?? 3306;
        $user = $this->config['username'];
        $pass = $this->config['password'];
        $db = $this->config['database'];
        
        $conn = @mysqli_connect($host, $user, $pass, $db, $port);
        if (!$conn) {
            throw new Exception(mysqli_connect_error());
        }
        
        $this->logger->success("Conexión MySQL exitosa a {$host}:{$port}/{$db}");
        mysqli_close($conn);
        return true;
    }
    
    /**
     * Prueba conexión PostgreSQL
     */
    private function testPostgreSQLConnection() {
        $host = $this->config['host'] ?? 'localhost';
        $port = $this->config['port'] ?? 5432;
        $user = $this->config['username'];
        $pass = $this->config['password'];
        $db = $this->config['database'];
        
        $connString = "host={$host} port={$port} dbname={$db} user={$user} password={$pass}";
        $conn = @pg_connect($connString);
        
        if (!$conn) {
            throw new Exception("No se pudo conectar a PostgreSQL");
        }
        
        $this->logger->success("Conexión PostgreSQL exitosa a {$host}:{$port}/{$db}");
        pg_close($conn);
        return true;
    }
    
    /**
     * Prueba conexión MongoDB
     */
    private function testMongoDBConnection() {
        if (!class_exists('MongoDB\Driver\Manager')) {
            throw new Exception("Extensión MongoDB no instalada");
        }
        
        $host = $this->config['host'] ?? 'localhost';
        $port = $this->config['port'] ?? 27017;
        $user = $this->config['username'] ?? '';
        $pass = $this->config['password'] ?? '';
        $db = $this->config['database'];
        
        $auth = ($user && $pass) ? "{$user}:{$pass}@" : "";
        $uri = "mongodb://{$auth}{$host}:{$port}/{$db}";
        
        $manager = new MongoDB\Driver\Manager($uri);
        $command = new MongoDB\Driver\Command(['ping' => 1]);
        $manager->executeCommand('admin', $command);
        
        $this->logger->success("Conexión MongoDB exitosa a {$host}:{$port}/{$db}");
        return true;
    }
    
    /**
     * Prueba conexión SQLite
     */
    private function testSQLiteConnection() {
        $dbPath = $this->config['database'];
        
        if (!file_exists($dbPath)) {
            throw new Exception("Archivo de base de datos SQLite no encontrado: {$dbPath}");
        }
        
        $conn = new SQLite3($dbPath, SQLITE3_OPEN_READONLY);
        if (!$conn) {
            throw new Exception("No se pudo abrir la base de datos SQLite");
        }
        
        $this->logger->success("Conexión SQLite exitosa: {$dbPath}");
        $conn->close();
        return true;
    }
    
    /**
     * Obtiene el tipo de base de datos
     */
    public function getDbType() {
        return $this->dbType;
    }
    
    /**
     * Obtiene la configuración
     */
    public function getConfig() {
        return $this->config;
    }
}
