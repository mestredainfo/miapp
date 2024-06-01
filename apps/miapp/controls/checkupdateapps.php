<?php
function getHTML(): string
{
    $url = 'https://mestredainfo.wordpress.com/apps/';

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $html = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Erro ao fazer a requisição: ' . curl_error($ch);
    }

    curl_close($ch);

    return $html;
}

function checkUpdate(string $nome): array
{
    $update = json_decode(file_get_contents(dirname(__FILE__, 3) . '/tmp/update.json'), true);
    $apps = [];

    foreach ($update as $row) {
        if ($row['nome'] == $nome) {
            if (file_exists(dirname(__FILE__, 3) . '/' . $nome . '/version')) {
                $version = file_get_contents(dirname(__FILE__, 3) . '/' . $nome . '/version');
                if (version_compare($row['versao'], $version)) {
                    $apps['nome'] = strtolower($row['nome']);
                    $apps['versao'] = $row['versao'];
                    $apps['url'] = $row['url'];
                    $apps['hash'] = $row['hash'];
                }
            }
            break;
        }
    }

    return $apps;
}


$html = getHTML();

// Usa regex para capturar o conteúdo dentro da <div> com o ID "aplicativos"
if (preg_match('/<ul id="miapps" class="wp-block-list">(.*?)<\/ul>/sm', $html, $matches)) {
    $conteudoDivAplicativos = $matches[1];
    // Expressão regular para extrair os dados relevantes
    $regex = '/<li>(\d+).*?Nome: (.*?)<\/li>.*?Descrição: (.*?)<\/li>.*?Versão: (.*?)<\/li>.*?Imagem: (.*?)<\/li>.*?URL: (.*?)<\/li>.*?Hash: (.*?)<\/li>/s';

    // Array para armazenar os aplicativos
    $apps = [];

    // Executa a expressão regular no HTML
    if (preg_match_all($regex, $conteudoDivAplicativos, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            // Extrai os dados correspondentes
            //$index = $match[1];

            $apps[] = [
                'nome' => strtolower($match[2]),
                'descricao' => $match[3],
                'versao' => $match[4],
                'imagem' => $match[5],
                'url' => $match[6],
                'hash' => $match[7]
            ];
        }

        file_put_contents(dirname(__FILE__, 3) . '/tmp/update.json', json_encode($apps));
    }
}
