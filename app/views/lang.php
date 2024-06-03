<?php


$milang = new milang();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Idioma</title>
</head>

<body>
    <?php
    echo $milang->traduzir('Este é um exemplo no PHP!');
    ?>
</body>

</html>