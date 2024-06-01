<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

header("Content-Security-Policy: default-src 'self'");
header("Content-Security-Policy: script-src 'self' 'unsafe-inline' script.js");

function checkUpdatea($a = false)
{
    try {
        if ($a) {
            echo 'Verificando atualizações...';
        }

        $url = 'https://mestredainfo.wordpress.com/miprotocolo/';

        $versaoatual = file_get_contents(dirname(__FILE__) . '/version');

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $html = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('Erro ao buscar os dados: ' . curl_error($ch));
        }

        curl_close($ch);

        preg_match('/<span id="appversion">(.*?)<\/span>/', $html, $matches);

        if (!empty($matches[1])) {
            $versaonova = $matches[1];

            if (version_compare($versaonova, $versaoatual, '>')) {
                echo "<script>
                if (confirm('Deseja baixar a nova versão? A versão $versaonova já está disponível para download.')) {
                    window.externo.rodar('$url');
                }";

                if ($a) {
                    echo "window.close();";
                }

                echo '</script>';
            } else {
                if ($a) {
                    echo "
                <script>
                window.alert('O software já está na versão mais recente.');
                window.close();
                </script>
                ";
                }
            }
        }
    } catch (Exception $error) {
        echo 'Erro ao buscar os dados: ' . $error->getMessage();
    }
}

if (!empty($_GET['a'])) {
    checkUpdate(true);
}
