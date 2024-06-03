<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

if (!defined('miapp')) {
    exit;
}

// Desativa o cache
header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Define o fuso horário
date_default_timezone_set("America/Sao_Paulo");

// Informa o nível dos erros que serão exibidos
error_reporting(E_ALL);

// Habilita a exibição de erros
ini_set("display_errors", 1);

// Configurações
$aConfig = json_decode(file_get_contents(dirname(__FILE__, 2) . '/config/config.json'), true);

// Verifica se a pasta do banco de dados existe, se não existir a mesma é criada
if ($aConfig['db']) {
    $processUser = posix_getpwuid(posix_geteuid());
    $db[] = '/home/' . $processUser['name'] . '/.' . str_replace(' ', '', strtolower($aConfig['app']['name'])) . '/data/' . str_replace(' ', '', strtolower($aConfig['app']['name'])) . '.sqlite';

    if (!file_exists(dirname($db[0]))) {
        mkdir(dirname($db[0]), 0777, true);
    }
}
