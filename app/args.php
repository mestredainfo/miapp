<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: MIT

// Mestre da Info
// Site: https://linktr.ee/mestreinfo
?>
<!DOCTYPE html>
<html lang="<?php echo $_ENV['MIAPP_LANG']; ?>">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arguments</title>
</head>

<body>
    <p>To get the arguments run the terminal or cmd, go to your folder and type:</p>
    <ul>
        <li>Linux: ./miapp test1 test2 test3</li>
        <li>Windows: miapp.exe test1 test2 test3</li>
    </ul>
    <?php
    if (empty($_ENV['MIAPP_ARGV'])) {
        echo 'No argument has been found!';
    } else {
        echo $_ENV['MIAPP_ARGV'];
    }
    ?>
</body>

</html>