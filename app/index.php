<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline'");
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIApp - Execute e Desenvolva aplicativos em PHP para desktop</title>
    <style>
        body {
            font-size: 18px;
        }

        li {
            line-height: 27px;
        }
    </style>
</head>

<body>
    <h3>Informações</h3>
    <p id="version" style="line-height: 37px;"></p>
    <p style="margin-top: -7px;">Versão do PHP: <?php echo phpversion(); ?></p>

    <h3>Exemplos</h3>
    <?php
    $files = scandir(dirname(__FILE__) . '/');
    echo '<ul>';
    foreach ($files as $file) {
        if (!empty($file)) {
            if ($file !== '.' && $file !== '..') {
                if (file_exists($file)) {
                    if ($file !== 'index.php' && $file !== 'lang' && $file !== 'includes' && $file !== 'dados' && $file !== 'menu') {
                        printf('<li><a href="%s" target="_blank" rel="noopener">%s</a></li>', $file, ucfirst(str_replace('.php', '', $file)));
                    }
                }
            }
        }
    }
    echo '<li><a href="#" onclick="window.miapp.novajanela(\'cookies.php\', 200, 200, false, false)">Nova Janela Personalizada</a></li>';
    echo '</ul>';
    ?>
    <script>
        const txtVersion = document.getElementById('version');

        window.miapp.versao('miapp').then((result) => {
            txtVersion.innerHTML = `Versão do MIApp: ${result}<br>`;
        });

        window.miapp.versao('electron').then((result) => {
            txtVersion.innerHTML += `Versão do ElectronJS: ${result}<br>`;
        });

        window.miapp.versao('node').then((result) => {
            txtVersion.innerHTML += `Versão do NodeJS: ${result}<br>`;
        });

        window.miapp.versao('chromium').then((result) => {
            txtVersion.innerHTML += `Versão do Chromium: ${result}<br>`;
        });

        function mensagem() {
            window.miapp.mensagem('MIApp', 'Está é uma mensagem!', 'info');
        }
    </script>
</body>

</html>