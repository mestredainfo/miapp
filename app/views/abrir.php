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
    <title>Abrir Arquivo</title>

    <link rel="stylesheet" href="/views/style.css">
</head>

<body>
    <?php
    if (!empty($_GET['filename'])) {
        $filename = filter_input(INPUT_GET, 'filename');

        echo '<textarea>' . file_get_contents($filename) . '</textarea>';
        exit;
    }
    ?>
    <script>
        async function abrir() {
            let sAbrir = await window.arquivo.abrir();
            window.location.assign(`?filename=${sAbrir.toString()}`);
        }
        abrir();
    </script>
</body>

</html>