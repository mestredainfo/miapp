<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: MIT

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'");

if (empty($_COOKIE['info'])) {
    $count = 1;
    setcookie('info[msg]', $count, 0, '/', $_SERVER['SERVER_NAME'], false, true);
} else {
    $count = $_COOKIE['info']['msg'] + 1;
    setcookie('info[msg]', $count, 0, '/', $_SERVER['SERVER_NAME'], false, true);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQLITE3 | MIApp</title>
</head>

<body>
    <?php
    $pathDados = dirname('__FILE__') . '/dados/';
    if (!file_exists($pathDados)) {
        mkdir($pathDados);
    }

    // Cria o banco de dados e a tabela
    $db1 = new SQLite3($pathDados . '/exemplo.sqlite');
    $db1->exec("CREATE TABLE IF NOT EXISTS mi_exemplo (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL
)");
    $db1->close();

    // Inseri registros
    $db2 = new SQLite3($pathDados . '/exemplo.sqlite', SQLITE3_OPEN_READWRITE);
    if (empty($_COOKIE['info'])) {
        $db2->query("INSERT INTO mi_exemplo (nome) VALUES ('" . $count . "')");
    } else {
        if ($stmt = $db2->prepare("INSERT INTO mi_exemplo (nome) VALUES (:nome)")) {
            $stmt->bindParam(':nome', $count);
            $stmt->execute();
            $stmt->close();
        }
    }
    $db2->close();

    echo '<p><a href="javascript:window.location.reload();">Atualizar Página</a></p>';

    // Consulta registros
    $db3 = new SQLite3($pathDados . '/exemplo.sqlite', SQLITE3_OPEN_READONLY);
    $query = $db3->query('SELECT * FROM mi_exemplo ORDER BY id DESC');
    while ($row = $query->fetchArray(SQLITE3_ASSOC)) {
        echo $row['nome'] . '<br>';
    }
    $db3->close();
    ?>
</body>

</html>