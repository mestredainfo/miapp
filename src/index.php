<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: MIT

// Mestre da Info
// Site: https://linktr.ee/mestreinfo

header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; script-src 'self' 'unsafe-inline'");
?>
<!DOCTYPE html>
<html lang="<?php echo $_ENV['MIAPP_LANG']; ?>">

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
    <p style="margin-top: -7px;">Version of PHP: <?php echo phpversion(); ?></p>

    <script>
        const txtVersion = document.getElementById('version');

        miapp.version('miapp').then((result) => {
            txtVersion.innerHTML = `Version of MIApp: ${result}<br>`;
        });

        miapp.version('electron').then((result) => {
            txtVersion.innerHTML += `Version of ElectronJS: ${result}<br>`;
        });

        miapp.version('node').then((result) => {
            txtVersion.innerHTML += `Version of NodeJS: ${result}<br>`;
        });

        miapp.version('chromium').then((result) => {
            txtVersion.innerHTML += `Version of Chromium: ${result}<br>`;
        });
    </script>
</body>

</html>