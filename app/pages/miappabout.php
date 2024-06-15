<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

if (!defined('miapp')) {
    exit;
}

global $miappConfig;
$miappConfig = json_decode(file_get_contents(dirname(__FILE__, 3) . '/package.json'), true);

function miappConfig(string ...$names): string|int|bool
{
    global $miappConfig;
    $sValor = $miappConfig;

    foreach ($names as $value) {
        $sValor = (empty($sValor[$value])) ? '' : $sValor[$value];
    }

    return $sValor;
}

$tpl = new miHTML();
echo $tpl->doctype();
echo $tpl->html(
    '',
    ['lang' => 'pt-BR'],
    $tpl->head(
        '',
        $tpl->meta('', ['charset' => 'UTF-8']),
        $tpl->meta('', ['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1.0']),
        $tpl->title(miappTranslate('Sobre o %s', miappConfig('name')))
    ),
    $tpl->body(
        '',
        $tpl->h1(miappTranslate('Sobre o %s', miappConfig('name'))),
        $tpl->p(miappTranslate('O MIApp é um software que executa aplicativos desenvolvidos em PHP no desktop.')),
        $tpl->p(miappTranslate('Compatível atualmente com Linux (Debian e Ubuntu) e Windows.')),
        $tpl->p(''),
        $tpl->p(miappConfig('name') . ' ' . miappConfig('version')),
        $tpl->p(miappTranslate('Desenvolvido por: %s', miappConfig('author', 'name'))),
        $tpl->p(miappTranslate('Organização: %s', miappConfig('author', 'organization'))),
        $tpl->p(
            'Site: ',
            $tpl->a(str_replace(['http://', 'https://'], '', miappConfig('homepage')), ['href' => sprintf("javascript:window.miapp.openURL('%s');", miappConfig('homepage'))])
        ),
        $tpl->p(miappConfig('copyright')),
        $tpl->p(miappTranslate('Licença: %s', miappConfig('license'))),
        $tpl->hr('', ['class' => 'border border-primary border-3 opacity-75']),
        $tpl->h3(miappTranslate('Recursos de Terceiros Utilizados')),
        $tpl->p(
            '',
            $tpl->strong('MIApp: '),
            $tpl->a('mestredainfo.wordpress.com/miapp/', ['href' => "javascript:window.miapp.openURL('https://mestredainfo.wordpress.com/miapp/');"])
        ),
        $tpl->p(
            '',
            $tpl->strong('ElectronJS: '),
            $tpl->a('electronjs.org', ['href' => "javascript:window.miapp.openURL('https://www.electronjs.org');"])
        ),
        $tpl->p(
            '',
            $tpl->strong('PHP: '),
            $tpl->a('php.net', ['href' => "javascript:window.miapp.openURL('https://www.php.net');"])
        )
    )
);
