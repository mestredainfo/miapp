<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

if (PHP_OS == 'Linux') {
    $sFolder = '/home/' . get_current_user() . '/.local/share/applications/';
    if (file_exists($sFolder)) {
        $sDesktop = '[Desktop Entry]
Version=' . $aConfig['update']['version'] . '
Name=' . $aConfig['app']['name'] . '
Comment=' . $aConfig['app']['description'] . '
Type=Application
Exec=' . dirname($documentroot, 4) . '/' . str_replace(' ', '', strtolower($aConfig['app']['name'])) . '
Icon=' . dirname($documentroot, 2) . '/icon/' . str_replace(' ', '', strtolower($aConfig['app']['name'])) . '.png
Categories=Utility;';

        $sCreateFile = file_put_contents($sFolder . '/' . str_replace(' ', '', strtolower($aConfig['app']['name'])) . '.desktop', $sDesktop);
        if ($sCreateFile) {
            echo '<script>window.alert(\'Atalho criado no menu iniciar!\');window.location.assign(\'index.php\');</script>';
        } else {
            echo '<script>window.alert(\'Não foi possível criar o atalho no menu iniciar!\');window.location.assign(\'index.php\');</script>';
        }
    } else {
        echo '<script>window.alert(\'Não foi possível criar o atalho no menu iniciar!\');window.location.assign(\'index.php\');</script>';
    }
} else {
    echo '<script>window.alert(\'No Windows você pode criar um atalho clicando com o botão direito no executável "' . str_replace(' ', '', strtolower($aConfig['app']['name'])) . '.exe" e clicando em "Criar Atalho"!\');window.location.assign(\'index.php\');</script>';
}
