<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: MIT

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'");
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Salvar Arquivo</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <?php
    if (!empty($_GET['filename'])) {
        $filename = filter_input(INPUT_GET, 'filename');

        file_put_contents($filename, rand(10000, 99999));
        echo 'Arquivo ' . basename($filename) . ' salvo com sucesso!';
        exit;
    }
    ?>
    <script>
        async function salvar() {
            let sSalvar = await window.miapp.saveFile();
            window.location.assign(`?filename=${sSalvar.toString()}`);
        }
        salvar();
    </script>
</body>

</html>