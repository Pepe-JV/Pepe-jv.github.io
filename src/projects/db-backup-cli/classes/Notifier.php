<?php

/**
 * Notifier Class
 * Maneja las notificaciones de las operaciones de backup (Slack, Email, etc.)
 */
class Notifier {
    private $config;
    private $logger;
    
    public function __construct($config, Logger $logger) {
        $this->config = $config;
        $this->logger = $logger;
    }
    
    /**
     * Envía notificación de operación de backup
     */
    public function notifyBackup($result, $dbName, $dbType) {
        $message = $this->buildBackupMessage($result, $dbName, $dbType);
        return $this->send($message, $result['success']);
    }
    
    /**
     * Envía notificación de operación de restauración
     */
    public function notifyRestore($result, $dbName, $dbType) {
        $message = $this->buildRestoreMessage($result, $dbName, $dbType);
        return $this->send($message, $result['success']);
    }
    
    /**
     * Construye el mensaje de notificación de backup
     */
    private function buildBackupMessage($result, $dbName, $dbType) {
        $status = $result['success'] ? '✅ ÉXITO' : '❌ FALLO';
        $emoji = $result['success'] ? ':white_check_mark:' : ':x:';
        
        $message = [
            'text' => "Backup de Base de Datos",
            'blocks' => [
                [
                    'type' => 'header',
                    'text' => [
                        'type' => 'plain_text',
                        'text' => "{$emoji} Backup de Base de Datos - {$status}"
                    ]
                ],
                [
                    'type' => 'section',
                    'fields' => [
                        [
                            'type' => 'mrkdwn',
                            'text' => "*Base de datos:*\n{$dbName}"
                        ],
                        [
                            'type' => 'mrkdwn',
                            'text' => "*Tipo:*\n{$dbType}"
                        ],
                        [
                            'type' => 'mrkdwn',
                            'text' => "*Duración:*\n{$result['duration']} segundos"
                        ],
                        [
                            'type' => 'mrkdwn',
                            'text' => "*Fecha:*\n" . date('Y-m-d H:i:s')
                        ]
                    ]
                ]
            ]
        ];
        
        if ($result['success'] && isset($result['filesize'])) {
            $size = $this->formatBytes($result['filesize']);
            $message['blocks'][] = [
                'type' => 'section',
                'fields' => [
                    [
                        'type' => 'mrkdwn',
                        'text' => "*Tamaño del archivo:*\n{$size}"
                    ],
                    [
                        'type' => 'mrkdwn',
                        'text' => "*Archivo:*\n`" . basename($result['filepath']) . "`"
                    ]
                ]
            ];
        }
        
        if (!$result['success'] && isset($result['error'])) {
            $message['blocks'][] = [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => "*Error:*\n```" . $result['error'] . "```"
                ]
            ];
        }
        
        return $message;
    }
    
    /**
     * Construye el mensaje de notificación de restauración
     */
    private function buildRestoreMessage($result, $dbName, $dbType) {
        $status = $result['success'] ? '✅ ÉXITO' : '❌ FALLO';
        $emoji = $result['success'] ? ':white_check_mark:' : ':x:';
        
        $message = [
            'text' => "Restauración de Base de Datos",
            'blocks' => [
                [
                    'type' => 'header',
                    'text' => [
                        'type' => 'plain_text',
                        'text' => "{$emoji} Restauración de Base de Datos - {$status}"
                    ]
                ],
                [
                    'type' => 'section',
                    'fields' => [
                        [
                            'type' => 'mrkdwn',
                            'text' => "*Base de datos:*\n{$dbName}"
                        ],
                        [
                            'type' => 'mrkdwn',
                            'text' => "*Tipo:*\n{$dbType}"
                        ],
                        [
                            'type' => 'mrkdwn',
                            'text' => "*Duración:*\n{$result['duration']} segundos"
                        ],
                        [
                            'type' => 'mrkdwn',
                            'text' => "*Fecha:*\n" . date('Y-m-d H:i:s')
                        ]
                    ]
                ]
            ]
        ];
        
        if (!$result['success'] && isset($result['error'])) {
            $message['blocks'][] = [
                'type' => 'section',
                'text' => [
                    'type' => 'mrkdwn',
                    'text' => "*Error:*\n```" . $result['error'] . "```"
                ]
            ];
        }
        
        return $message;
    }
    
    /**
     * Envía la notificación al canal configurado
     */
    private function send($message, $isSuccess) {
        if (!isset($this->config['enabled']) || !$this->config['enabled']) {
            return true;
        }
        
        $type = $this->config['type'] ?? 'slack';
        
        try {
            switch ($type) {
                case 'slack':
                    return $this->sendToSlack($message);
                case 'email':
                    return $this->sendEmail($message, $isSuccess);
                default:
                    $this->logger->warning("Tipo de notificación no soportado: {$type}");
                    return false;
            }
        } catch (Exception $e) {
            $this->logger->error("Error al enviar notificación: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envía notificación a Slack
     */
    private function sendToSlack($message) {
        if (!isset($this->config['webhook_url'])) {
            $this->logger->warning("Webhook URL de Slack no configurado");
            return false;
        }
        
        $webhookUrl = $this->config['webhook_url'];
        $payload = json_encode($message);
        
        $ch = curl_init($webhookUrl);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($payload)
        ]);
        
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode == 200) {
            $this->logger->success("Notificación enviada a Slack exitosamente");
            return true;
        } else {
            $this->logger->error("Error al enviar notificación a Slack. HTTP Code: {$httpCode}");
            return false;
        }
    }
    
    /**
     * Envía notificación por email
     */
    private function sendEmail($message, $isSuccess) {
        if (!isset($this->config['email_to'])) {
            $this->logger->warning("Email destinatario no configurado");
            return false;
        }
        
        $to = $this->config['email_to'];
        $subject = $isSuccess ? 
            "[BACKUP] Operación completada exitosamente" : 
            "[BACKUP] Operación fallida";
        
        $body = $this->formatEmailBody($message);
        $headers = [
            'From: ' . ($this->config['email_from'] ?? 'backup@localhost'),
            'Content-Type: text/html; charset=UTF-8'
        ];
        
        if (mail($to, $subject, $body, implode("\r\n", $headers))) {
            $this->logger->success("Notificación enviada por email exitosamente");
            return true;
        } else {
            $this->logger->error("Error al enviar email");
            return false;
        }
    }
    
    /**
     * Formatea el mensaje para email HTML
     */
    private function formatEmailBody($message) {
        $html = "<html><body>";
        $html .= "<h2>" . $message['text'] . "</h2>";
        
        foreach ($message['blocks'] as $block) {
            if ($block['type'] === 'header') {
                $html .= "<h3>" . $block['text']['text'] . "</h3>";
            } elseif ($block['type'] === 'section') {
                $html .= "<table border='1' cellpadding='10'>";
                if (isset($block['fields'])) {
                    foreach ($block['fields'] as $field) {
                        $text = strip_tags($field['text']);
                        $html .= "<tr><td>{$text}</td></tr>";
                    }
                }
                if (isset($block['text'])) {
                    $text = strip_tags($block['text']['text']);
                    $html .= "<tr><td>{$text}</td></tr>";
                }
                $html .= "</table><br>";
            }
        }
        
        $html .= "</body></html>";
        return $html;
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
}
