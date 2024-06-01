<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

/* Anti SQL INJECTION */
function CleanDB(?string $valor): string|int|null
{
    if (is_null($valor)) {
        $txt = '';
    } else {
        $txt = trim($valor);
        $txt = strip_tags($txt);
        $txt = addslashes($txt);
    }

    return $txt;
}

/* Limpar GET */
function CleanGET(string $nome, int $filter = FILTER_DEFAULT): string|int|null
{
    return filter_input(INPUT_GET, $nome, $filter);
}

function emptyGET(string $nome, array|int $options = 0): bool
{
    return empty(filter_input(INPUT_GET, $nome, FILTER_DEFAULT, $options)) ? true : false;
}

/* Limpar POST */
function CleanPOST(string $nome, int $filter = FILTER_DEFAULT): string|int|null
{
    return filter_input(INPUT_POST, $nome, $filter);
}

function emptyPOST(string $nome, array|int $options = 0): bool
{
    return empty(filter_input(INPUT_POST, $nome, FILTER_DEFAULT, $options)) ? true : false;
}

function requestPOST(): bool
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        return true;
    } else {
        return false;
    }
}

function requestGET(): bool
{
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        return true;
    } else {
        return false;
    }
}

function servername(): string
{
    $servername = 'http://' . CleanDB($_SERVER['SERVER_NAME']) . ':' . CleanDB($_SERVER['SERVER_PORT']);

    return $servername;
}

function requestURI(): string
{
    return rtrim(parse_url(CleanDB($_SERVER['REQUEST_URI']), PHP_URL_PATH), '/');
}

/* Exibe Alertas */
function windowAlert(string $message)
{
    echo sprintf("<script>window.alert('%s');</script>", $message);
}

/* Redireciona */
function redirect(string $url, mixed $params = '')
{
    $sParams = '?';

    if (is_array($params)) {
        foreach ($params as $name => $value) {
            $sParams .= sprintf('%s=%s&', $name, $value);
        }
    } else {
        $sParams = '';
    }

    $sParams = rtrim($sParams, '&');

    echo sprintf("<script>window.location.assign('%s%s');</script>", $url, $sParams);
    exit;
}

/* Verifica Arrays */
function verificarArray(string $haystack, mixed $needle): bool
{
    /* Gera array caso for detectado uma string e não um array */
    if (!is_array($needle)) {
        $needle = array($needle);
    }

    foreach ($needle as $query) {
        if (strpos($haystack, $query, 0) !== false) {
            /* Retorna verdadeiro e para a repetição ao encontrar o resultado */
            return true;
        }
    }

    return false;
}

function removerAcentos(string $valor): string
{
    $array1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç", "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç");
    $array2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c", "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C");
    return str_replace($array1, $array2, $valor);
}

/* Remover caracteres especiais de um texto */
function removerCaracteresEspeciais(string $valor): string
{
    $array1 = array("$", "@", "%", "&", "*", "/", "+", "#");
    $array2 = array("", "", "", "", "", "", "", "");
    return str_replace($array1, $array2, $valor);
}

/* Exibe arrays formatados com tag pre */
function pre($value)
{
    printf('<pre>%s</pre>', print_r($value, true));
}