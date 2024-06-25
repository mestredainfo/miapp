<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: MIT

// Mestre da Info
// Site: https://linktr.ee/mestreinfo

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'");

if (empty($_COOKIE['info'])) {
    $count = 1;
    setcookie('info[msg]', $count, 0, '/', $_SERVER['SERVER_NAME'], false, true);
} else {
    $count = $_COOKIE['info']['msg'] + 1;
    setcookie('info[msg]', $count, 0, '/', $_SERVER['SERVER_NAME'], false, true);
}
?>
<!DOCTYPE html>
<html lang="<?php echo $_ENV['MIAPP_LANG']; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cookies | MIApp</title>
</head>

<body>
    <?php
    echo 'Cookie: ' . $count;
    echo '<p><a href="javascript:window.location.reload();">Update Page</a></p>';
    ?>
</body>

</html>