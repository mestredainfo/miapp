<?php
header("Content-Security-Policy: default-src 'self'");
header("Content-Security-Policy: script-src 'self' 'unsafe-inline'");

include_once(dirname(__FILE__) . '/controls/checkupdateapps.php');
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MIApp</title>
    <link rel="stylesheet" href="/miapp/plugins/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/miapp/css/style.css">
</head>

<body>
    <?php include_once(dirname(__FILE__) . '/menu/menu.php'); ?>
    <div class="container-fluid">
        <h3>Lista de Aplicativos</h3>
        <table class="table">
            <thead>
                <th>Aplicativos</th>
                <th>Ações</th>
            </thead>
            <tbody>
                <?php
                $sApps = scandir(dirname(__FILE__, 2));

                if (!empty($sApps)) {
                    foreach ($sApps as $sPath) {
                        if ($sPath !== '.' && $sPath !== '..') {
                            if ($sPath !== 'index.php' && $sPath !== 'miapp' && $sPath !== 'tmp') {
                ?>
                                <tr>
                                    <td class="w-50"><?php printf('<a href="/%s/" target="_blank">%s</a>', $sPath, $sPath); ?></td>
                                    <td>
                                        <a href="/miapp/pages/criaratalho.php" class="btn btn-primary">Criar Atalho</a>&nbsp;&nbsp;
                                        <?php
                                        $cUpdate = checkUpdate($sPath);
                                        if (!empty($cUpdate)) { ?>
                                            <a href="/miapp/pages/atualizar.php?<?php echo http_build_query($cUpdate, '', '&'); ?>" class="btn btn-success">Atualizar</a>&nbsp;&nbsp;
                                        <?php } ?>
                                        <a href="#" onclick="removerApp('<?php echo $sPath; ?>')" class="btn btn-danger">Desinstalar</a>
                                    </td>
                                </tr>
                <?php
                            }
                        }
                    }
                }
                ?>
            </tbody>
        </table>
        <script src="/miapp/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script>
            function removerApp(nome) {
                window.miapp.mensagem('Desinstalar App', `Você deseja realmente desinstalar o "${nome}"?`, 'question', true).then((result) => {
                    if (!result) {
                        window.location.assign(`/miapp/pages/remover.php?app=${nome}`);
                    }
                });
            }
        </script>
</body>

</html>