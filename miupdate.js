// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

module.exports = class miupdate {
    constructor(milang) {
        this.sMILang = milang
    }

    async getNewVersion() {
        try {
            const https = require('https');

            return new Promise((resolve, reject) => {
                const options = {
                    hostname: 'mestredainfo.wordpress.com',
                    path: '/miapp/',
                    method: 'GET'
                };

                const req = https.request(options, (res) => {
                    let data = '';

                    res.on('data', (chunk) => {
                        const match = data.match(/<span id="appversion">([^<]+)<\/span>/);

                        if (match) {
                            const versaonova = match[1].trim();
                            resolve(versaonova);
                            req.destroy();
                            return;
                        } else {
                            data += chunk;
                        }
                    });
                });

                req.on('error', (error) => {
                    reject(new Error(this.sMILang.miappTraduzir('Erro ao fazer a solicitação HTTP: %s', error.message)));
                });

                req.end();
            });
        } catch (error) {
            console.error(this.sMILang.miappTraduzir('Erro ao buscar os dados:'), error);
        }
    }

    checkUpdate() {
        this.getNewVersion()
            .then((versaonova) => {
                const versaoatual = require('electron').app.getVersion();

                if (versaonova > versaoatual) {
                    const options = {
                        type: 'question',
                        buttons: [this.sMILang.miappTraduzir('Mais tarde'), this.sMILang.miappTraduzir('Atualizar Agora')],
                        title: this.sMILang.miappTraduzir('Atualização do MIApp'),
                        message: this.sMILang.miappTraduzir('Deseja baixar a nova versão do MIApp?\nA versão %s já está disponível para baixar.', versaonova)
                    };

                    require('electron').dialog.showMessageBox(null, options).then(retorno => {
                        if (retorno.response === 1) {
                            require('electron').shell.openExternal('https://mestredainfo.wordpress.com/miapp/');
                        }
                    });
                }
            })
            .catch((error) => {
                console.error(this.sMILang.miappTraduzir('Erro ao buscar os dados:'), error);
            });
    }
}