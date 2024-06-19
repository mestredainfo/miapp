<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: MIT

// Mestre da Info
// Site: https://linktr.ee/mestreinfo

header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline'");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIApp - Run and develop PHP applications for desktop</title>
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
    <h3>Information</h3>
    <p id="version" style="line-height: 37px;"></p>
    <p style="margin-top: -7px;">PHP version: <?php echo phpversion(); ?></p>

    <h3>Examples</h3>
    <?php
    $files = scandir(dirname(__FILE__) . '/');
    echo '<ul>';
    foreach ($files as $file) {
        if (!empty($file)) {
            if ($file !== '.' && $file !== '..') {
                if (file_exists($file) && $file !== 'index.php' && $file !== 'style.css') {
                    if (is_file($file)) {
                        printf('<li><a href="%s" target="_blank" rel="noopener">%s</a></li>', $file, ucfirst(str_replace('.php', '', $file)));
                    }
                }
            }
        }
    }
    echo '</ul>';
    ?>
    <script>
        const txtVersion = document.getElementById('version');

        window.miapp.version('miapp').then((result) => {
            txtVersion.innerHTML = `Version of MIApp: ${result}<br>`;
        });

        window.miapp.version('electron').then((result) => {
            txtVersion.innerHTML += `Version of ElectronJS: ${result}<br>`;
        });

        window.miapp.version('node').then((result) => {
            txtVersion.innerHTML += `Version of NodeJS: ${result}<br>`;
        });

        window.miapp.version('chromium').then((result) => {
            txtVersion.innerHTML += `Version of Chromium: ${result}<br>`;
        });
    </script>
</body>

</html>