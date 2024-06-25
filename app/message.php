<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: MIT

// Mestre da Info
// Site: https://linktr.ee/mestreinfo

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <button type="button" onclick="message()">Display message</button>
    <button type="button" onclick="confirm()">Confirm message</button>
    <div id="info"></div>
    <script>
        async function message() {
            miapp.alert('Information message', 'This is an example of a message!', 'info', false);
        }

        async function confirm() {
            miapp.confirm('Confirmation message', 'This is an example of a message!', 'error', true).then((result) => {
                if (result) {
                    document.getElementById('info').innerHTML = 'Not confirmed';
                } else {
                    document.getElementById('info').innerHTML = 'Confirmed';
                }
            });
        }
    </script>
</body>

</html>