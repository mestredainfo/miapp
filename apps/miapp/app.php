<?php
header("Content-Security-Policy: default-src 'self'");
header("Content-Security-Policy: script-src 'self' 'unsafe-inline'");
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIApp</title>
    <link rel="stylesheet" href="design/style.css">
</head>

<body>
    <h1>Aplicativos</h1>
    <?php
    $sApps = scandir(dirname(__FILE__, 2));

    if (!empty($sApps)) {
        foreach ($sApps as $sPath) {
            if ($sPath !== '.' && $sPath !== '..') {
                if ($sPath !== 'index.php' && $sPath !== 'miapp') {
                    printf('<a href="/%s/" target="_blank">%s</a><br>', $sPath, $sPath);
                }
            }
        }
    }
    ?>
</body>

</html>