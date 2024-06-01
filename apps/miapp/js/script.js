// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

/**
 * @requires miapp
 */
function post(url, data, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('POST', url, true);

    xhr.onreadystatechange = function () {
        if (this.status == 200) {
            callback(this.responseText);
        }
    }

    xhr.send(data);
}

function abrirExterno(url) {
    window.externo.rodar(url);
}