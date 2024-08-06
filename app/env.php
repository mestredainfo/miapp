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
    <title>Env</title>
</head>
<body>
    <strong>Username</strong><br>
    <?php echo $_ENV['MIAPP_USERNAME']; ?><br><br>

    <strong>User Path</strong><br>
    <?php echo $_ENV['MIAPP_USERPATH']; ?><br><br>

    <strong>Platform</strong><br>
    <?php echo $_ENV['MIAPP_PLATFORM']; ?><br><br>

    <strong>Language</strong><br>
    <?php echo $_ENV['MIAPP_LANG']; ?><br><br>

    <strong>App Path</strong><br>
    <?php echo $_ENV['MIAPP_PATH']; ?><br><br>
</body>
</html>