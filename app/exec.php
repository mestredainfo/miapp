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
    <title>Execute Command</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <label>Command</label><br>
    <input id="command" type="text" value="dir <?php echo $_ENV['MIAPP_PATH']; ?>">
    <br><br>
    <button type="button" onclick="exec()">Exec</button>
    <hr>
    <div id="info"></div>
    <script>
        async function exec() {
            document.getElementById('info').innerHTML = '';
            miapp.exec(document.getElementById('command').value);
            miapp.listExec((data) => {
                var sData = data.split("\n")
                sData.forEach(result => {
                    document.getElementById('info').innerHTML += result + '<br>';
                });
            });
        }
    </script>
</body>

</html>