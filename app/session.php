<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: MIT

// Mestre da Info
// Site: https://linktr.ee/mestreinfo

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'");

session_name('miapp');
session_start();
?>
<!DOCTYPE html>
<html lang="<?php echo $_ENV['MIAPP_LANG']; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session | MIApp</title>
</head>

<body>
    <?php
    if (empty($_SESSION['info'])) {
        $count = 1;
        $_SESSION['info'] = $count;

        echo 'Session: ' . $count;
    } else {
        $count = $_SESSION['info'] + 1;
        $_SESSION['info'] = $count;

        echo 'Session: ' . $count;
    }

    echo '<p><a href="javascript:window.location.reload();">Update Page</a></p>';
    ?>
</body>

</html>