<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'");
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulário POST</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!empty($_POST['txtNome'])) {
            $txtNome = filter_input(INPUT_POST, 'txtNome');

            echo 'Seu nome é ' . $txtNome . '<hr>';
        }
    }
    ?>
    <form name="frmPost" method="post" action="formpost.php">
        <div>
            <label for="txtNome">Digite seu nome</label>
            <input id="txtNome" name="txtNome" type="text" placeholder="Digite seu nome" required>
        </div>
        <button type="submit">Enviar</button>
    </form>
</body>

</html>