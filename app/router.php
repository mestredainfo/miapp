<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

include_once(dirname(__FILE__) . '/libs/miapplibs.php');

if (empty(miRequestURI())) {
    include_once(miPathRoot() . '/home.php');
} else {
    $p = pathinfo(miRequestURI());

    if (!empty($p['extension']) && $p['extension'] == 'php') {
        if (miRequestURI() == 'micreateshortcut.php') {
            miCreateShortcut();
        } elseif (miRequestURI() == 'micheckupdate.php') {
            miCheckUpdate(true);
        } else {
            include_once(miPathRoot() . DIRECTORY_SEPARATOR . miRequestURI());
        }
    } else {
        return false;
    }
}
