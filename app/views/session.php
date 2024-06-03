<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo



session_name('miapp');
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">

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

    echo '<p><a href="javascript:window.location.reload();">Atualizar Página</a></p>';
    ?>
</body>

</html>