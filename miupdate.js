// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

module.exports = class milang {
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
                    reject(new Error(this.sMILang.traduzir('Erro ao fazer a solicitação HTTP: ') + error.message));
                });

                req.end();
            });
        } catch (error) {
            console.error(this.sMILang.traduzir('Erro ao buscar os dados:'), error);
        }
    }

    checkUpdate() {
        this.getNewVersion()
            .then((versaonova) => {
                const versaoatual = require('electron').app.getVersion();

                if (versaonova > versaoatual) {
                    const options = {
                        type: 'question',
                        buttons: [this.sMILang.traduzir('Mais tarde'), this.sMILang.traduzir('Atualizar Agora')],
                        title: this.sMILang.traduzir('Atualização do MIApp'),
                        message: this.sMILang.traduzir('Deseja baixar a nova versão do MIApp?') + "\n" + this.sMILang.traduzir('A versão ') + versaonova + this.sMILang.traduzir(' já está disponível para baixar.')
                    };

                    require('electron').dialog.showMessageBox(null, options).then(retorno => {
                        if (retorno.response === 1) {
                            require('electron').shell.openExternal('https://mestredainfo.wordpress.com/miapp/');
                        }
                    });
                }
            })
            .catch((error) => {
                console.error(this.sMILang.traduzir('Erro ao buscar os dados:'), error);
            });
    }
}