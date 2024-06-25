// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: MIT

// Mestre da Info
// Site: https://linktr.ee/mestreinfo

const { ipcMain, dialog, BrowserWindow } = require('electron')

module.exports = {
    mifunctions: function (milang, miappNewWindow) {
        // Função para selecionar pasta
        ipcMain.handle('selecionarDiretorio', async () => {
            const { canceled, filePaths } = await dialog.showOpenDialog({ properties: ['openDirectory'] });
            if (!canceled) {
                return filePaths[0];
            }
        });

        // Função para abrir arquivo
        ipcMain.handle('abrirArquivo', async () => {
            const { canceled, filePaths } = await dialog.showOpenDialog({ properties: ['openFile'] });
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
        ipcMain.handle('appMessage', async (event, title, msg, type, ...buttons) => {
            let sButtons = [milang.traduzir('Continuar'), ...buttons];

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

        // Função para caixa de confirmação
        ipcMain.handle('appConfirm', async (event, title, msg, type, ...buttons) => {
            let sButtons = [milang.traduzir('Continuar'), milang.traduzir('Cancelar'), ...buttons];

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

        // Abre uma nova janela personalizada
        ipcMain.handle('appNewWindow', async (event, url, width, height, resizable, frame, menu, hide) => {
            miappNewWindow(url, width, height, resizable, frame, menu, hide);
        });

        // Traduzir
        ipcMain.handle('appTraduzir', async (event, text, ...values) => {
            return milang.traduzir(text, ...values);
        });

        // DevTools
        ipcMain.handle('openDevTools', async (event) => {
            BrowserWindow.getFocusedWindow().webContents.openDevTools();
        });

        // AppNotification
        ipcMain.handle('appNotification', async (event, title, text) => {
            let { Notification } = require('electron');
            new Notification({ title: title, body: text }).show();
        });

        // AppTray
        ipcMain.handle('appTray', async (event, title, tooltip, image, menu) => {
            const { Tray, Menu, nativeImage } = require('electron')
            const icon = nativeImage.createFromPath(image)
            let tray = new Tray(icon)
            const contextMenu = Menu.buildFromTemplate(menu);
            tray.setContextMenu(contextMenu);
            tray.setToolTip(tooltip);
            tray.setTitle(title);
        });

        // AppTray
        ipcMain.handle('appExportPDF', async (event, filename, options) => {
            const fs = require('fs');
            const pdfPath = filename;
            
            let pdfOptions = options;
            if (!pdfOptions) {
                pdfOptions = {
                    pageSize: 'A4'
                };
            }

            BrowserWindow.getFocusedWindow().webContents.printToPDF(pdfOptions).then(data => {
                fs.writeFile(pdfPath, data, (error) => {
                    if (error) throw error
                    console.log(milang.traduzir('PDF salvo com sucesso em %s', pdfPath))
                })
            }).catch(error => {
                console.log(milang.traduzir('Erro ao tentar gerar o PDF em %s', pdfPath), error)
            })
        });

        // AppExec
        ipcMain.handle('appExec', async (event, command) => {
            var childProcess = require('child_process');
            const child = childProcess.exec(command);

            child.stdout.on('data', (d) => {
                BrowserWindow.getFocusedWindow().webContents.send('list:exec', d);
            });

            child.stdout.on('close', () => {
                child.unref();
                child.kill();
            });
        });
    }
}