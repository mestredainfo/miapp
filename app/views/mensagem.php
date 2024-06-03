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
    <title>Mensagem</title>

    <link rel="stylesheet" href="style.css">
</head>

<body>
    <button type="button" onclick="mensagem()">Exibir Mensagem</button>
    <button type="button" onclick="confirm()">Confirmar Mensagem</button>
    <div id="info"></div>
    <script>
        async function mensagem() {
            window.miapp.mensagem('Mensagem de Informação', 'Este é um exemplo de mensagem!', 'info', false);
        }

        async function confirm() {
            window.miapp.mensagem('Mensagem de Confirmação', 'Este é um exemplo de mensagem!', 'error', true).then((result) => {
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