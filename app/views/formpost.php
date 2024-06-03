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
    <title><?php echo $milang->traduzir('Formulário'); ?> POST</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (!empty($_POST['txtNome'])) {
            $txtNome = filter_input(INPUT_POST, 'txtNome');

            echo $milang->traduzir('Seu nome é ') . $txtNome . '<hr>';
        }
    }
    ?>
    <form name="frmPost" method="post" action="formpost.php?checkupdate=no">
        <div>
            <label for="txtNome"><?php echo $milang->traduzir('Digite seu nome'); ?></label>
            <input id="txtNome" name="txtNome" type="text" placeholder="<?php echo $milang->traduzir('Digite seu nome'); ?>" required>
        </div>
        <button type="submit">Enviar</button>
    </form>
</body>

</html>