<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

define('miapp', true);

include_once(dirname(__FILE__) . '/libs/miapplibs.php');

function miappshow(): bool
{
    if (empty(miRequestURI())) {
        include_once(miPathRoot() . '/home.php');
        return true;
    } else {
        if (miFileExtension(miRequestURI()) == 'php') {
            if (miRequestURI() == 'miappabout.php') {
                include_once(dirname(__FILE__) . '/pages/miappabout.php');
                return true;
            } elseif (miRequestURI() == 'micreateshortcut.php') {
                miCreateShortcut();
                return true;
            } elseif (miRequestURI() == 'micheckupdate.php') {
                miCheckUpdate(true);
                return true;
            } else {
                if (file_exists(miPathRoot() . DIRECTORY_SEPARATOR . miRequestURI())) {
                    include_once(miPathRoot() . DIRECTORY_SEPARATOR . miRequestURI());
                    return true;
                } else {
                    echo miappTranslate('Arquivo "%s" não encontrado.', basename(miRequestURI()));
                    return true;
                }
            }
        } elseif (!miFileExtension(miRequestURI()) && miConfig('router') == 'wf2h#f2ubci*2f2b3rrb2@idiu32b%db23u') {
            include_once(miPathRoot() . '/home.php');
            return true;
        } else {
            return false;
        }
    }
}

$miapp = miappshow();
if (!$miapp) {
    return false;
}
