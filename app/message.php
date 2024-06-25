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
    <title>Message</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <button type="button" onclick="message()">Display message</button>
    <button type="button" onclick="message(true)">Display (3 Buttons)</button>
    <button type="button" onclick="confirm()">Confirm message</button>
    <button type="button" onclick="confirm(true)">Confirm (4 Buttons)</button>
    <div id="info"></div>
    <script>
        async function message(buttons) {
            if (buttons) {
                miapp.alert('Information message', 'This is an example of a message!', 'info', 'Botão 1', 'Botão 3');
            } else {
                miapp.alert('Information message', 'This is an example of a message!', 'info');
            }
        }

        async function confirm(buttons) {
            if (buttons) {
                miapp.confirm('Confirmation message', 'This is an example of a message!', 'error', 'Botão 1', 'Botão 2').then((result) => {
                    if (result == 1) {
                        document.getElementById('info').innerHTML = 'Not confirmed';
                    } else if (result == 2) {
                        document.getElementById('info').innerHTML = 'Button 1';
                    } else if (result == 3) {
                        document.getElementById('info').innerHTML = 'Button 2';
                    } else {
                        document.getElementById('info').innerHTML = 'Confirmed';
                    }
                });
            } else {
                miapp.confirm('Confirmation message', 'This is an example of a message!', 'error').then((result) => {
                    if (result) {
                        document.getElementById('info').innerHTML = 'Not confirmed';
                    } else {
                        document.getElementById('info').innerHTML = 'Confirmed';
                    }
                });
            }
        }
    </script>
</body>

</html>