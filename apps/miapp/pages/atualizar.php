<?php
function downloadFile($url, $destination): bool
{
    try {
        $txt = false;
        $sURL = 'https://' . $url;

        // Inicializar cURL
        $ch = curl_init($sURL);

        // Abrir o arquivo local para escrita
        $fileHandle = fopen($destination, 'w');

        // Configurar as opções do cURL
        curl_setopt($ch, CURLOPT_FILE, $fileHandle);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        // Executar a requisição cURL
        curl_exec($ch);

        // Verificar se ocorreu algum erro durante o download
        if (curl_errno($ch)) {
            echo 'Erro durante o download: ' . curl_error($ch) . '<br>';
            $error = true;
        } else {
            // Exibir uma mensagem indicando que o download foi concluído
            $txt = true;
        }

        // Fechar o arquivo
        fclose($fileHandle);

        // Fechar a sessão cURL
        curl_close($ch);

        return $txt;
    } catch (Exception $ex) {
        echo $ex->getMessage();
        return false;
    }
}

function installApp($sZip, $sName, $sHash): bool
{
    $txt = false;
    $fileHash = hash_file('sha256', $sZip);
 
    if ($fileHash == $sHash) {
        // Criar uma instância da classe ZipArchive
        $zip = new ZipArchive();

        // Tentar abrir o arquivo ZIP
        if ($zip->open($sZip) === TRUE) {
            $zip->extractTo(dirname(__FILE__, 3));
            $zip->close();

            unlink($sZip);

            $txt = true;
        } else {
            echo 'Arquivo de instalação não localizado!';
            unlink($sZip);
        }
    } else {
        echo 'Hash do arquivo incorreto!';
        unlink($sZip);
    }

    return $txt;
}

$sURL = $_GET['url'];
$sDestination = dirname(__FILE__, 3) . '/tmp/' . $_GET['nome'] . '-' . time() . '.zip';
echo 'Baixando atualização...<br>';
if (downloadFile($sURL, $sDestination)) {
    echo 'Instalando...<br>';
    if (installApp($sDestination, $_GET['nome'], $_GET['hash'])) {
        echo 'Concluido';
    }
}

echo '<a href="javascript:window.location.assign(\'/\');">Atualizar</a>';
exit;
