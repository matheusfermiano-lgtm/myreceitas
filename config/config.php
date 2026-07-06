<?php
// config/config.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define o caminho absoluto da pasta raiz do projeto
define('ROOT_PATH', str_replace('\\', '/', dirname(__DIR__)));

// Detecta a URL base (ex: /myreceitas/) para links e assets
$docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$projetoDir = str_replace($docRoot, '', ROOT_PATH);
$baseUrl = '/' . trim($projetoDir, '/') . '/';
if ($baseUrl === '//') { $baseUrl = '/'; }

define('BASE_URL', $baseUrl);
define('UPLOAD_PATH', BASE_URL . 'assets/uploads/');