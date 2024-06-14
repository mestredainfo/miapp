<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

include_once(dirname(__FILE__) . '/libs/miapplibs.php');

if (empty(miRequestURI())) {
    include_once(miPathRoot() . '/home.php');
} else {
    if (miFileExtension(miRequestURI()) == 'php') {
        if (miRequestURI() == 'micreateshortcut.php') {
            miCreateShortcut();
        } elseif (miRequestURI() == 'micheckupdate.php') {
            miCheckUpdate(true);
        } else {
            if (file_exists(miPathRoot() . DIRECTORY_SEPARATOR . miRequestURI())) {
                include_once(miPathRoot() . DIRECTORY_SEPARATOR . miRequestURI());
            } else {
                echo miTranslate('Arquivo "%s" não encontrado.', basename(miRequestURI()));
            }
        }
    } else {
        return false;
    }
}
