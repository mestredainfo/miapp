<?php

use MIApp\lang;

if (!defined('miapp')) {
    exit;
}

/* Idioma */
include_once($documentroot . '/classes/language/lang.php');

/* Banco de Dados */
include_once($documentroot . '/classes/database/database.php');
include_once($documentroot . '/classes/database/select.php');
include_once($documentroot . '/classes/database/insert.php');
include_once($documentroot . '/classes/database/update.php');
include_once($documentroot . '/classes/database/delete.php');

/* Configurações */
include_once($documentroot . '/core/config.php');

/* Funções */
include_once($documentroot . '/core/funcoes.php');

$milang = new lang();

/* Verifica Atualizações */
if (empty($_GET['checkupdate'])) {
    include_once($documentroot . '/controls/checkupdate.php');
    checkUpdate();
    redirect('index.php?checkupdate=no');
} else {
    if ($_GET['checkupdate'] == 'yes') {
        include_once($documentroot . '/controls/checkupdate.php');
        checkUpdate();
    }
}

if (requestURI() == 'sobre.php') {
    include_once($documentroot . '/controls/sobre.php');
}

if (requestURI() == 'createdesktop.php') {
    include_once($documentroot . '/controls/criaratalho.php');
    exit;
}

/* Rotas */
include_once(includeviews());
