// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

const { ipcMain, dialog } = require('electron');

module.exports = {
    mifunctions: function (win, miappNewWindow) {
        // Função para abrir arquivo
        ipcMain.handle('abrirArquivo', async () => {
            const { canceled, filePaths } = await dialog.showOpenDialog({});
            if (!canceled) {
                return filePaths[0];
            }
        });

        // Função para salvar arquivo
        ipcMain.handle('salvarArquivo', async () => {
            const { canceled, filePath } = await dialog.showSaveDialog({});
            if (!canceled) {
                return filePath;
            }
        });

        // Abrir aplicativo externo
        ipcMain.handle('appExterno', async (event, url) => {
            require('electron').shell.openExternal(url);
        });

        // Obter versão do aplicativo e recursos
        ipcMain.handle('appVersao', async (event, tipo) => {
            if (tipo == 'miapp') {
                return require('electron').app.getVersion();
            } else if (tipo == 'electron') {
                return process.versions.electron;
            } else if (tipo == 'node') {
                return process.versions.node;
            } else if (tipo == 'chromium') {
                return process.versions.chrome;
            } else {
                return '';
            }
        });

        // Função para caixa de alerta
        ipcMain.handle('appMessage', async (event, title, msg, type, confirm) => {
            let sButtons;

            if (confirm) {
                sButtons = ['Continuar', 'Cancelar'];
            } else {
                sButtons = ['Continuar'];
            }

            let options = {
                type: type,
                buttons: sButtons,
                defaultId: 1,
                cancelId: 2,
                title: title,
                message: msg
            }
            return dialog.showMessageBoxSync(null, options);
        });
    
        // Nova Janela
        ipcMain.handle('appNovaJanela', async (event, url, width, height, resizable, menu) => {
           miappNewWindow(url, width, height, resizable, menu);
        });
    }
}