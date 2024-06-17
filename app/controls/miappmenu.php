<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

class miappmenu
{
    private array $menu = [];

    public function init()
    {
        $menus = json_decode(file_get_contents(miPathRoot() . '/menu/menu.json'), true);

        $i1 = 0;
        $i2 = 0;
        foreach ($menus as $name => $value) {
            $this->menu[$i1]['menu'] = $name;
            foreach ($value as $name2 => $value2) {
                if ($name2 == 'separator') {
                    $this->menu[$i1]['submenu'][$i2]['name'] = 'separator';
                } else {
                    $this->menu[$i1]['submenu'][$i2]['name'] = $name2;
                }

                foreach ($value2 as $name3 => $value3) {
                    $this->menu[$i1]['submenu'][$i2][$name3] = $value3;
                }

                $i2 += 1;
            }

            $i1 += 1;
        }
    }

    public function create()
    {
        $this->init();

        $txt = '';
        foreach ($this->menu as $menu) {
            $txt .= '<li><a href="#">' . $menu['menu'] . '</a>';
            if (!empty($menu['submenu'])) {
                $txt .= '<ul>';
                foreach ($menu['submenu'] as $submenu) {
                    $datakey = '';
                    if (!empty($submenu['key'])) {
                        if ($submenu['key']) {
                            $datakey = ' data-key="' . $submenu['key'] . '"';
                        }
                    }

                    if ($submenu['name'] == 'separator') {
                        $txt .= '<li><hr></li>';
                    } else {
                        if (isset($submenu['newwindow'])) {
                            $sPage = (empty($submenu['page'])) ? 'about:blank,' : "'{$submenu['page']}',";
                            $sPage .= (empty($submenu['width'])) ? '800,' : "{$submenu['width']},";
                            $sPage .= (empty($submenu['height'])) ? '600,' : "{$submenu['height']},";

                            if (isset($submenu['resizable'])) {
                                if ($submenu['resizable']) {
                                    $sPage .= 'true,';
                                } else {
                                    $sPage .= 'false,';
                                }
                            } else {
                                $sPage .= 'true,';
                            }
                            if (isset($submenu['menu'])) {
                                if ($submenu['menu']) {
                                    $sPage .= 'true,';
                                } else {
                                    $sPage .= 'false,';
                                }
                            } else {
                                $sPage .= 'true,';
                            }
                            if (isset($submenu['hide'])) {
                                if ($submenu['hide']) {
                                    $sPage .= 'true,';
                                } else {
                                    $sPage .= 'false,';
                                }
                            } else {
                                $sPage .= 'true,';
                            }

                            $sPage = rtrim($sPage, ',');

                            $sJS = "javascript:miapp.newwindow($sPage);";
                            $txt .= '<li><a href="' . $sJS . '"' . $datakey . '>' . $submenu['name'] . ' <span class="miappmenukey">' . str_replace(['data-key="', '"'], '', $datakey) . '</span></a></li>';
                        } else {
                            if (empty($submenu['url'])) {
                                if (empty($submenu['script'])) {
                                    if ($submenu['page'] == '/') {
                                        $txt .= '<li><a href="' . $submenu['page'] . '"' . $datakey . '>' . $submenu['name'] . ' <span class="miappmenukey">' . str_replace(['data-key="', '"'], '', $datakey) . '</span></a></li>';
                                    } else {

                                        $txt .= '<li><a href="/' . $submenu['page'] . '"' . $datakey . '>' . $submenu['name'] . ' <span class="miappmenukey">' . str_replace(['data-key="', '"'], '', $datakey) . '</span></a></li>';
                                    }
                                } else {
                                    $txt .= '<li><a href="javascript:' . $submenu['script'] . '"' . $datakey . '>' . $submenu['name'] . ' <span class="miappmenukey">' . str_replace(['data-key="', '"'], '', $datakey) . '</span></a></li>';
                                }
                            } else {
                                $txt .= '<li><a href="javascript:miapp.openURL(\'' . $submenu['url'] . '\');"' . $datakey . '>' . $submenu['name'] . ' <span class="miappmenukey">' . str_replace(['data-key="', '"'], '', $datakey) . '</span></a></li>';
                            }
                        }
                    }
                }
                $txt .= '</ul>';
            }
            $txt .= '</li>';
        }

        return $txt;
    }
}

$miappMenu = new miappmenu();
?>
<ul id="miappmenu" class="miappmenu">
    <?php
    if (miConfig('dev', 'menu')) {
    ?>
        <li><a href="#"><?php echo miappTranslate('Dev'); ?></a>
            <ul>
                <li><a href="javascript:location.reload();" data-key="F5"><?php echo miappTranslate('Atualizar'); ?> <span class="miappmenukey">F5</span></a></li>
                <li>
                    <hr>
                </li>
                <li><a href="javascript:miappinit.devTools();" data-key="F12"><?php echo miappTranslate('DevTools'); ?> <span class="miappmenukey">F12</span></a></li>
            </ul>
        </li>
    <?php
    }

    if (miConfig('app', 'menu')) {
        $miappMenu->init();
        echo $miappMenu->create();
    }
    ?>

    <li style="float:right;position:absolute;right:10"><a href="#">MIApp</a>
        <ul style="right: 0px" ;>
            <li><a href="javascript:miappinit.checkupdate();"><?php echo miappTranslate('Verificar Atualização'); ?></a></li>
            <li>
                <hr>
            </li>
            <li><a href="javascript:miapp.openURL('https://www.mestredainfo.com.br/2024/06/documentacao-miapp.html');"><?php echo miappTranslate('Documentação'); ?></a></li>
            <li><a href="javascript:miapp.openURL('https://www.mestredainfo.com.br/p/assinantes.html');"><?php echo miappTranslate('Assinantes'); ?></a></li>
            <li><a href="javascript:miapp.openURL('https://www.mestredainfo.com.br/p/suporte.html');"><?php echo miappTranslate('Suporte'); ?></a></li>
            <li>
                <hr>
            </li>
            <li><a href="javascript:miapp.newwindow('/miappabout.php', 800, 400, false, false, false);"><?php echo miappTranslate('Sobre o MIApp'); ?></a></li>
        </ul>
    </li>
</ul>

<!-- Menu de Contexto -->
<div id="miAppContextMenu">
    <ul>
        <li id="miappCutAction">Recortar</li>
        <li id="miappCopyAction">Copiar</li>
        <li id="miappPasteAction">Colar</li>
        <hr>
        <li id="miappSelectAllAction">Selecionar Tudo</li>
    </ul>
</div>