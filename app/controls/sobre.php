<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

function aboutApp($texto = '', $bootstrap = false): string
{
    global $aConfig, $milang;
    $txt = '<h1>' . $milang->traduzir('Sobre o App') . '</h1>
<p>' . $aConfig['app']['name'] . ' ' . $aConfig['app']['version'] . '</p>
<p>' . $milang->traduzir('Desenvolvido por:') . ' ' . $aConfig['author']['name']  . '</p>
<p>' . $milang->traduzir('Organização:') . ' ' . $aConfig['author']['organization'] . '</p>
<p>Site: <a href="javascript:window.externo.rodar(\'' . $aConfig['homepage'] . '\');">' . str_replace(['http://','https://'], '', $aConfig['homepage']) . '</a></p>

<p>' . $aConfig['copyright'] . '</p>

<p>' . $milang->traduzir('Licença:') . ' ' . $aConfig['license'] . '</p>

<hr class="border border-primary border-3 opacity-75">

<h3>' . $milang->traduzir('Recursos de Terceiros Utilizados') . '</h3>

<p><strong>MIApp:</strong> <a href="javascript:window.externo.rodar(\'https://mestredainfo.wordpress.com/miapp/\');">mestredainfo.wordpress.com/miapp/</a></p>

<p><strong>ElectronJS:</strong> <a href="javascript:window.externo.rodar(\'https://www.electronjs.org\');">electronjs.org</a></p>

<p><strong>PHP:</strong> <a href="javascript:window.externo.rodar(\'https://www.php.net\');">php.net</a></p>';

    if ($bootstrap) {
        $txt .= '<p><strong>Bootstrap:</strong> <a href="javascript:window.externo.rodar(\'https://getbootstrap.com\');">getbootstrap.com</a></p>';
    }

    $txt .= $texto;

    return $txt;
}
