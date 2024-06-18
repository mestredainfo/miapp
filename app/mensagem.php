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
    <title>Mensagem</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <button type="button" onclick="mensagem()">Exibir Mensagem</button>
    <button type="button" onclick="confirm()">Confirmar Mensagem</button>
    <div id="info"></div>
    <script>
        async function mensagem() {
            window.miapp.alert('Mensagem de Informação', 'Este é um exemplo de mensagem!', 'info', false);
        }

        async function confirm() {
            window.miapp.confirm('Mensagem de Confirmação', 'Este é um exemplo de mensagem!', 'error', true).then((result) => {
                console.log(result);
                if (result) {
                    document.getElementById('info').innerHTML = 'Não Confirmado';
                } else {
                    document.getElementById('info').innerHTML = 'Confirmado';
                }
            });
        }
    </script>
</body>

</html>