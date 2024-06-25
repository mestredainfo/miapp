<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: MIT

// Mestre da Info
// Site: https://linktr.ee/mestreinfo

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'");
?>
<!DOCTYPE html>
<html lang="<?php echo $_ENV['MIAPP_LANG']; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <button type="button" onclick="notification()">Notification</button>
    <div id="info"></div>
    <script>
        async function notification() {
            miapp.notification('Information message', 'This is an example of a message!');
        }
    </script>
</body>

</html>