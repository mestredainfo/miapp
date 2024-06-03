<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

if ($_SERVER['REQUEST_URI'] == '/' || strpos($_SERVER['REQUEST_URI'], '.php') !== false) {
    define('miapp', true);

    $documentroot = dirname(__FILE__);

    include_once($documentroot . '/core/app.php');
} else {
    return false;
}
