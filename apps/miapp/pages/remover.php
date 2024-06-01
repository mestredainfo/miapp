<?php

function excluirArquivos($caminho)
{
    // Verifica se o caminho existe
    if (!file_exists($caminho)) {
        return false;
    }

    // Se o caminho é um diretório, itera sobre os arquivos e pastas dentro dele
    if (is_dir($caminho)) {
        $diretorio = new DirectoryIterator($caminho);
        foreach ($diretorio as $item) {
            if (!$item->isDot()) { // Ignora "." e ".."
                if ($item->isDir()) {
                    // Se é um diretório, chama recursivamente a função para excluí-lo
                    excluirArquivos($item->getPathname());
                } else {
                    // Se é um arquivo, exclui-o
                    unlink($item->getPathname());
                }
            }
        }
        // Após excluir todos os arquivos e pastas, exclui o diretório
        rmdir($caminho);
        return true;
    } else {
        // Se o caminho é um arquivo, exclui-o diretamente
        unlink($caminho);
        return true;
    }
}

if (!empty($_GET['app'])) {
    echo 'Desinstalando...';
    $sApp = filter_input(INPUT_GET, 'app', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    excluirArquivos(dirname(__FILE__, 3) . '/' . $sApp);
    echo "<script>window.miapp.mensagem('', 'Aplicativo {$_GET['app']} desinstalado com sucesso!');window.location.assign('/');</script>";
}
