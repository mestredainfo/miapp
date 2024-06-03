<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário GET</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        if (!empty($_GET['txtNome'])) {
            $txtNome = filter_input(INPUT_GET, 'txtNome');

            echo 'Seu nome é ' . $txtNome . '<hr>';
        }
    }
    ?>
    <form name="frmPost" method="get" action="formget.php">
        <div>
            <label for="txtNome">Digite seu nome</label>
            <input id="txtNome" name="txtNome" type="text" placeholder="Digite seu nome" required>
        </div>
        <button type="submit">Enviar</button>
    </form>
</body>

</html>