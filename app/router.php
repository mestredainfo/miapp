<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

define('miapp', true);

include_once(dirname(__FILE__) . '/libs/miapplibs.php');

$miappInit = function () {
    echo '<link rel="stylesheet" href="/css/miappstyle.css">';
    include_once(dirname(__FILE__) . '/controls/miappmenu.php');
    echo '<script src="/js/miappscript.js"></script>';
};

function miappshow(): bool
{
    global $miappInit;
    if (empty(miRequestURI())) {
        $miappInit();
        include_once(miPathRoot() . '/home.php');
        return true;
    } elseif (miRequestURI() == 'css/miappstyle.css') {
        echo file_get_contents(dirname(__FILE__) . '/css/miappstyle.css');
        return true;
    } elseif (miRequestURI() == 'js/miappscript.js') {
        echo file_get_contents(dirname(__FILE__) . '/js/miappscript.js');
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
                    $miappInit();
                    include_once(miPathRoot() . DIRECTORY_SEPARATOR . miRequestURI());
                    return true;
                } else {
                    echo miappTranslate('Arquivo "%s" não encontrado.', basename(miRequestURI()));
                    return true;
                }
            }
        } elseif (!miFileExtension(miRequestURI()) && miConfig('router')) {
            $miappInit();
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
