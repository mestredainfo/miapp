<?php
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

/* Rotas */
include_once(includeviews());