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
    <title>File Open</title>

    <link rel="stylesheet" href="style.css">
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
        async function open() {
            let sOpen = await miapp.openFile();
            window.location.assign(`?filename=${sOpen.toString()}`);
        }
        open();
    </script>
</body>

</html>