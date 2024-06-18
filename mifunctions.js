// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: MIT

// Mestre da Info
// Site: https://linktr.ee/mestreinfo

const { ipcMain, dialog, BrowserWindow } = require('electron')

module.exports = {
    mifunctions: function (milang, miappNewWindow) {
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
                sButtons = [milang.traduzir('Continuar'), milang.traduzir('Cancelar')];
            } else {
                sButtons = [milang.traduzir('Continuar')];
            }

            let options = {
                type: type,
                buttons: sButtons,
                defaultId: 1,
                cancelId: 2,
                title: milang.traduzir(title),
                message: milang.traduzir(msg)
            }
            return dialog.showMessageBoxSync(null, options);
        });

        // Abre uma nova janela personalizada
        ipcMain.handle('appNewWindow', async (event, url, width, height, resizable, menu, hide) => {
            miappNewWindow(url, width, height, resizable, menu, hide);
        });

        // Traduzir
        ipcMain.handle('appTraduzir', async (event, text, ...values) => {
            return milang.traduzir(text, ...values);
        });

        // DevTools
        ipcMain.handle('openDevTools', async (event) => {
            BrowserWindow.getFocusedWindow().webContents.openDevTools();
        });
    }
}