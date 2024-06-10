<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

/* Clean */
function miCleanDB(?string $valor): string|int|null
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

function miCleanENV(string $name): string
{
    return miCleanDB(filter_input(INPUT_ENV, $name, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
}

/* Lang */
$miLangSystem = miCleanENV('miapplangsystem');
$miLangPath = miCleanENV('miapppathroot') . '/lang/' . $miLangSystem . '.json';

$miLang = [];

if (file_exists($miLangPath)) {
    $miLang = json_decode(file_get_contents($miLangPath), true);
} else {
    if (file_exists(miCleanENV('miapppathroot') . '/lang/en.json')) {
        $miLang = json_decode(file_get_contents(miCleanENV('miapppathroot') . '/lang/en.json'), true);
    } else {
        $miLang = [];
    }
}

function miTranslate(string $text): string
{
    global $miLang;
    return (empty($miLang[$text])) ? $text : $miLang[$text];
}

/* Config */
$miaConfig = json_decode(file_get_contents(miCleanENV('miapppathroot') . '/config/config.json'), true);

function miConfig(string ...$names): string|int|bool
{
    global $miaConfig;

    $sValor = $miaConfig;

    foreach ($names as $value) {
        $sValor = (empty($sValor[$value])) ? '' : $sValor[$value];
    }

    return $sValor;
}

/* Limpar GET */
function miCleanGET(string $nome, int $filter = FILTER_DEFAULT): string|int|null
{
    return filter_input(INPUT_GET, $nome, $filter);
}

function miEmptyGET(string $nome, array|int $options = 0): bool
{
    return empty(filter_input(INPUT_GET, $nome, FILTER_DEFAULT, $options)) ? true : false;
}

/* Limpar POST */
function miCleanPOST(string $nome, int $filter = FILTER_DEFAULT): string|int|null
{
    return filter_input(INPUT_POST, $nome, $filter);
}

function miEmptyPOST(string $nome, array|int $options = 0): bool
{
    return empty(filter_input(INPUT_POST, $nome, FILTER_DEFAULT, $options)) ? true : false;
}

function miRequestPOST(): bool
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        return true;
    } else {
        return false;
    }
}

function miRequestGET(): bool
{
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        return true;
    } else {
        return false;
    }
}

function miServerName(): string
{
    $servername = 'http://' . miCleanDB($_SERVER['SERVER_NAME']) . ':' . miCleanDB($_SERVER['SERVER_PORT']);

    return $servername;
}

function miRequestURI(): string
{
    $sRequestURI = $_SERVER['REQUEST_URI'];
    $sRequestURI = miCleanDB($sRequestURI);
    $txt = explode('?', $sRequestURI);
    return empty($txt[0]) ? '' : ltrim($txt[0], '/');
}

function miIsLinux(): bool
{
    return (miCleanENV('miappplatform') == 'linux') ? true : false;
}

/* Exibe Alertas */
function miAlert(string $title, string $message, string $type, bool $inscript = false)
{
    global $milang;

    if (!$inscript) {
        echo '<script>';
    }

    echo sprintf("window.miapp.alert('%s', '%s', '%s');", miTranslate($title), miTranslate($message), $type);

    if (!$inscript) {
        echo '</script>';
    }
}

/* Exibe Confirmação */
function miConfirm(string $title, string $message, string $type, mixed $functionContinue, mixed $functionCancel, bool $inscript = false)
{
    global $milang;
    if (!$inscript) {
        echo '<script>';
    }

    echo "window.miapp.confirm('" . miTranslate($title) . "', '" . miTranslate($message) . "', '$type', true).then((result) => {
        console.log(result);
        if (result) {
            ";
    $functionCancel();
    echo "} else {";
    $functionContinue();
    echo "}
    });";

    if (!$inscript) {
        echo '</script>';
    }
}

/* Redireciona */
function miRedirect(string $url, mixed $params = '', bool $inscript = false)
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

    if (!$inscript) {
        echo '<script>';
    }

    echo sprintf("window.location.assign('%s%s');", $url, $sParams);

    if (!$inscript) {
        echo '</script>';
    }

    exit;
}

function miOpenURL(string $url, bool $inscript = false)
{
    if (!$inscript) {
        echo '<script>';
    }
    echo "window.miapp.openURL('$url');";

    if (!$inscript) {
        echo '</script>';
    }
}

function miWindowClose($inscript = false)
{
    if (!$inscript) {
        echo '<script>';
    }

    echo 'window.close();';

    if (!$inscript) {
        echo '</script>';
    }
}

function miVerificarArray(string $haystack, mixed $needle): bool
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

function miCheckArray(string $keyword,mixed $values) {
    miVerificarArray($keyword, $values);
}

function miRemoveAccents(string $valor): string
{
    $array1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç", "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç");
    $array2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c", "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C");
    return str_replace($array1, $array2, $valor);
}

/* Remover caracteres especiais de um texto */
function miRemoveSpecialCharacters(string $valor): string
{
    $array1 = array("$", "@", "%", "&", "*", "/", "+", "#");
    $array2 = array("", "", "", "", "", "", "", "");
    return str_replace($array1, $array2, $valor);
}

/* Exibe arrays formatados com tag pre */
function miPre($value)
{
    printf('<pre>%s</pre>', print_r($value, true));
}

// Sobre o App
function miAboutApp($texto = '', $bootstrap = false): string
{
    global $milang;
    $txt = '<h1>' . miTranslate('Sobre o ') . miConfig('app', 'name') . '</h1>
<p>' . miConfig('app', 'name') . ' ' . miConfig('app', 'version') . '</p>
<p>' . miTranslate('Desenvolvido por:') . ' ' . miConfig('author', 'name')  . '</p>
<p>' . miTranslate('Organização:') . ' ' . miConfig('author', 'organization') . '</p>
<p>Site: <a href="javascript:window.miapp.openURL(\'' . miConfig('homepage') . '\');">' . str_replace(['http://', 'https://'], '', miConfig('homepage')) . '</a></p>

<p>' . miConfig('copyright') . '</p>

<p>' . miTranslate('Licença:') . ' ' . miConfig('license') . '</p>

<hr class="border border-primary border-3 opacity-75">

<h3>' . miTranslate('Recursos de Terceiros Utilizados') . '</h3>

<p><strong>MIApp:</strong> <a href="javascript:window.miapp.openURL(\'https://mestredainfo.wordpress.com/miapp/\');">mestredainfo.wordpress.com/miapp/</a></p>

<p><strong>ElectronJS:</strong> <a href="javascript:window.miapp.openURL(\'https://www.electronjs.org\');">electronjs.org</a></p>

<p><strong>PHP:</strong> <a href="javascript:window.miapp.openURL(\'https://www.php.net\');">php.net</a></p>';

    if ($bootstrap) {
        $txt .= '<p><strong>Bootstrap:</strong> <a href="javascript:window.miapp.openURL(\'https://getbootstrap.com\');">getbootstrap.com</a></p>';
    }

    $txt .= $texto;

    return $txt;
}

function miUsername(): string
{
    return miCleanENV('miappusername');
}

function miUserPath(): string
{
    return miCleanENV('miappuserpath');
}

function miPathRoot()
{
    return miCleanENV('miapppathroot');
}

function miAppName()
{
    return miConfig('app', 'name');
}

function miCreateShortcut()
{
    if (miIsLinux()) {
        $sFolder = miUserPath() . '/.local/share/applications/';

        if (file_exists($sFolder)) {
            $tplShortcut = file_get_contents(dirname(__FILE__) . '/templates/shortcut.txt');
            $tplShortcut = str_replace('{version}', miConfig('app', 'version'), $tplShortcut);
            $tplShortcut = str_replace('{name}', miConfig('app', 'name'), $tplShortcut);
            $tplShortcut = str_replace('{description}', miTranslate(miConfig('app', 'description')), $tplShortcut);
            $tplShortcut = str_replace('{exec}', dirname(miPathRoot()) . "/" . str_replace(' ', '', strtolower(miAppName())), $tplShortcut);
            $tplShortcut = str_replace('{icon}', miPathRoot() . "/icon/" . str_replace(' ', '', strtolower(miAppName())) . ".png", $tplShortcut);
            $tplShortcut = str_replace('{categories}', miConfig('app', 'categories'), $tplShortcut);

            $sCreateFile = file_put_contents($sFolder . '/' . str_replace(' ', '', strtolower(miAppName())) . '.desktop', $tplShortcut);
            if ($sCreateFile) {
                miAlert(miTranslate('Informação ') . miConfig('app', 'name'), 'Atalho criado no menu iniciar', 'info');
            } else {
                miAlert(miTranslate('Informação ') . miConfig('app', 'name'), 'Não foi possível criar o atalho no menu iniciar!', 'error');
            }
        } else {
            miAlert(miTranslate('Informação ') . miConfig('app', 'name'), 'Não foi possível criar o atalho no menu iniciar!', 'error');
        }
    } else {
        miAlert(miTranslate('Informação ') . miConfig('app', 'name'), miTranslate('No Windows você pode criar um atalho clicando com o botão direito no executável \"') . str_replace(' ', '', strtolower(miConfig('app', 'name'))) . '.exe\" e clicando em \"Criar Atalho\"!', 'error');
    }

    miWindowClose();
}

function miCheckUpdate($show = false)
{
    try {
        if ($show) {
            echo miTranslate('Verificando atualizações...');
        }

        $url = miConfig('update', 'url');

        $versaoatual = miConfig('app', 'version');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $html = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception(miTranslate('Erro ao buscar os dados: ') . curl_error($ch));
        }

        curl_close($ch);

        preg_match('/<span id="appversion">(.*?)<\/span>/', $html, $matches);

        if (!empty($matches[1])) {
            $versaonova = $matches[1];

            if (version_compare($versaonova, $versaoatual, '>')) {
                miConfirm(miTranslate('Atualização do ') . miConfig('app', 'name'), 'Deseja baixar a nova versão?', 'question', function () use ($url, $show) {
                    miOpenURL($url, true);
                    if ($show) {
                        miWindowClose(true);
                    }
                }, function () use ($show) {
                    if ($show) {
                        miWindowClose(true);
                    }
                });
            } else {
                if ($show) {
                    miAlert(miTranslate('Atualização do ') . miConfig('app', 'name'), 'O software já está na versão mais recente.', 'info');
                    miWindowClose();
                }
            }
        }
    } catch (Exception $error) {
        echo miTranslate('Erro ao buscar os dados: ') . $error->getMessage();
    }
}

include_once(dirname(__FILE__) . '/database/database.php');
include_once(dirname(__FILE__) . '/database/select.php');
include_once(dirname(__FILE__) . '/database/insert.php');
include_once(dirname(__FILE__) . '/database/update.php');
include_once(dirname(__FILE__) . '/database/delete.php');
