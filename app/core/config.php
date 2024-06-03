<?php
if (!defined('miapp')) {
    exit;
}

// Desativa o cache
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Políticas de Segurança de Conteúdo
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline'");

// Define o fuso horário
date_default_timezone_set("America/Sao_Paulo");

// Informa o nível dos erros que serão exibidos
error_reporting(E_ALL);

// Habilita a exibição de erros
ini_set("display_errors", 1);

// Configurações
$aConfig = json_decode(file_get_contents(dirname(__FILE__, 2) . '/config/config.json'), true);

$config['app'] = $aConfig['app']['name'];
$config['description'] = $aConfig['app']['description'];
$config['checkupdate'] = $aConfig['update']['url'];;

// Verifica se a pasta do banco de dados existe, se não existir a mesma é criada
if ($aConfig['db']) {
    $config['db'] = '/home/' . get_current_user() . '/.' . str_replace(' ', '', strtolower($config['app'])) . '/data/' . str_replace(' ', '', strtolower($config['app'])) . '.sqlite';

    if (!file_exists(dirname($config['app']))) {
        mkdir(dirname($config['app']), 0777, true);
    }
}
