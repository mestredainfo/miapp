<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: MIT

// Mestre da Info
// Site: https://linktr.ee/mestreinfo

if ($_SERVER['REQUEST_URI'] == '/') {
    include_once(dirname(__FILE__) . '/index.php');
} else {
    if (strstr($_SERVER['REQUEST_URI'], '.php')) {
        if (file_exists(dirname(__FILE__) . '/' . $_SERVER['REQUEST_URI'])) {
            include_once(dirname(__FILE__) . '/' . $_SERVER['REQUEST_URI']);
        } else {
            return false;
        }
    } else {
        return false;
    }
}
