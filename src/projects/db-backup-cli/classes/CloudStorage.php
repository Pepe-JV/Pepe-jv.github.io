<?php

/**
 * CloudStorage Class
 * Maneja el almacenamiento de backups en servicios cloud (AWS S3, etc.)
 */
class CloudStorage {
    private $provider;
    private $config;
    private $logger;
    
    public function __construct($provider, $config, Logger $logger) {
        $this->provider = strtolower($provider);
        $this->config = $config;
        $this->logger = $logger;
    }
    
    /**
     * Sube un archivo de backup al almacenamiento cloud
     */
    public function upload($localPath, $remotePath = null) {
        if (!file_exists($localPath)) {
            $this->logger->error("Archivo local no encontrado: {$localPath}");
            return false;
        }
        
        if ($remotePath === null) {
            $remotePath = basename($localPath);
        }
        
        try {
            switch ($this->provider) {
                case 's3':
                case 'aws':
                    return $this->uploadToS3($localPath, $remotePath);
                case 'gcs':
                case 'google':
                    return $this->uploadToGCS($localPath, $remotePath);
                case 'azure':
                    return $this->uploadToAzure($localPath, $remotePath);
                default:
                    throw new Exception("Proveedor cloud no soportado: {$this->provider}");
            }
        } catch (Exception $e) {
            $this->logger->error("Error al subir a cloud: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Sube archivo a AWS S3 usando AWS CLI
     */
    private function uploadToS3($localPath, $remotePath) {
        $bucket = $this->config['bucket'];
        $region = $this->config['region'] ?? 'us-east-1';
        
        $this->logger->info("Subiendo a AWS S3: s3://{$bucket}/{$remotePath}");
        
        $command = sprintf(
            'aws s3 cp %s s3://%s/%s --region %s 2>&1',
            escapeshellarg($localPath),
            escapeshellarg($bucket),
            escapeshellarg($remotePath),
            escapeshellarg($region)
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new Exception("Error al subir a S3: " . implode("\n", $output));
        }
        
        $this->logger->success("Archivo subido exitosamente a S3");
        return true;
    }
    
    /**
     * Sube archivo a Google Cloud Storage usando gsutil
     */
    private function uploadToGCS($localPath, $remotePath) {
        $bucket = $this->config['bucket'];
        
        $this->logger->info("Subiendo a Google Cloud Storage: gs://{$bucket}/{$remotePath}");
        
        $command = sprintf(
            'gsutil cp %s gs://%s/%s 2>&1',
            escapeshellarg($localPath),
            escapeshellarg($bucket),
            escapeshellarg($remotePath)
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new Exception("Error al subir a GCS: " . implode("\n", $output));
        }
        
        $this->logger->success("Archivo subido exitosamente a GCS");
        return true;
    }
    
    /**
     * Sube archivo a Azure Blob Storage usando az CLI
     */
    private function uploadToAzure($localPath, $remotePath) {
        $container = $this->config['container'];
        $account = $this->config['account'];
        
        $this->logger->info("Subiendo a Azure Blob Storage: {$account}/{$container}/{$remotePath}");
        
        $command = sprintf(
            'az storage blob upload --account-name %s --container-name %s --name %s --file %s 2>&1',
            escapeshellarg($account),
            escapeshellarg($container),
            escapeshellarg($remotePath),
            escapeshellarg($localPath)
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new Exception("Error al subir a Azure: " . implode("\n", $output));
        }
        
        $this->logger->success("Archivo subido exitosamente a Azure");
        return true;
    }
    
    /**
     * Descarga un archivo desde el almacenamiento cloud
     */
    public function download($remotePath, $localPath) {
        try {
            switch ($this->provider) {
                case 's3':
                case 'aws':
                    return $this->downloadFromS3($remotePath, $localPath);
                case 'gcs':
                case 'google':
                    return $this->downloadFromGCS($remotePath, $localPath);
                case 'azure':
                    return $this->downloadFromAzure($remotePath, $localPath);
                default:
                    throw new Exception("Proveedor cloud no soportado: {$this->provider}");
            }
        } catch (Exception $e) {
            $this->logger->error("Error al descargar desde cloud: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Descarga archivo desde AWS S3
     */
    private function downloadFromS3($remotePath, $localPath) {
        $bucket = $this->config['bucket'];
        $region = $this->config['region'] ?? 'us-east-1';
        
        $this->logger->info("Descargando desde AWS S3: s3://{$bucket}/{$remotePath}");
        
        $command = sprintf(
            'aws s3 cp s3://%s/%s %s --region %s 2>&1',
            escapeshellarg($bucket),
            escapeshellarg($remotePath),
            escapeshellarg($localPath),
            escapeshellarg($region)
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new Exception("Error al descargar desde S3: " . implode("\n", $output));
        }
        
        $this->logger->success("Archivo descargado exitosamente desde S3");
        return true;
    }
    
    /**
     * Descarga archivo desde Google Cloud Storage
     */
    private function downloadFromGCS($remotePath, $localPath) {
        $bucket = $this->config['bucket'];
        
        $this->logger->info("Descargando desde Google Cloud Storage: gs://{$bucket}/{$remotePath}");
        
        $command = sprintf(
            'gsutil cp gs://%s/%s %s 2>&1',
            escapeshellarg($bucket),
            escapeshellarg($remotePath),
            escapeshellarg($localPath)
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new Exception("Error al descargar desde GCS: " . implode("\n", $output));
        }
        
        $this->logger->success("Archivo descargado exitosamente desde GCS");
        return true;
    }
    
    /**
     * Descarga archivo desde Azure Blob Storage
     */
    private function downloadFromAzure($remotePath, $localPath) {
        $container = $this->config['container'];
        $account = $this->config['account'];
        
        $this->logger->info("Descargando desde Azure Blob Storage: {$account}/{$container}/{$remotePath}");
        
        $command = sprintf(
            'az storage blob download --account-name %s --container-name %s --name %s --file %s 2>&1',
            escapeshellarg($account),
            escapeshellarg($container),
            escapeshellarg($remotePath),
            escapeshellarg($localPath)
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new Exception("Error al descargar desde Azure: " . implode("\n", $output));
        }
        
        $this->logger->success("Archivo descargado exitosamente desde Azure");
        return true;
    }
    
    /**
     * Lista los archivos de backup en el almacenamiento cloud
     */
    public function listBackups($prefix = '') {
        try {
            switch ($this->provider) {
                case 's3':
                case 'aws':
                    return $this->listS3Backups($prefix);
                case 'gcs':
                case 'google':
                    return $this->listGCSBackups($prefix);
                case 'azure':
                    return $this->listAzureBackups($prefix);
                default:
                    throw new Exception("Proveedor cloud no soportado: {$this->provider}");
            }
        } catch (Exception $e) {
            $this->logger->error("Error al listar backups: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Lista backups en S3
     */
    private function listS3Backups($prefix) {
        $bucket = $this->config['bucket'];
        $region = $this->config['region'] ?? 'us-east-1';
        
        $command = sprintf(
            'aws s3 ls s3://%s/%s --region %s 2>&1',
            escapeshellarg($bucket),
            escapeshellarg($prefix),
            escapeshellarg($region)
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new Exception("Error al listar S3: " . implode("\n", $output));
        }
        
        return $output;
    }
    
    /**
     * Lista backups en GCS
     */
    private function listGCSBackups($prefix) {
        $bucket = $this->config['bucket'];
        
        $command = sprintf(
            'gsutil ls gs://%s/%s 2>&1',
            escapeshellarg($bucket),
            escapeshellarg($prefix)
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new Exception("Error al listar GCS: " . implode("\n", $output));
        }
        
        return $output;
    }
    
    /**
     * Lista backups en Azure
     */
    private function listAzureBackups($prefix) {
        $container = $this->config['container'];
        $account = $this->config['account'];
        
        $command = sprintf(
            'az storage blob list --account-name %s --container-name %s --prefix %s --output table 2>&1',
            escapeshellarg($account),
            escapeshellarg($container),
            escapeshellarg($prefix)
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new Exception("Error al listar Azure: " . implode("\n", $output));
        }
        
        return $output;
    }
}
