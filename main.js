// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

const { app, BrowserWindow, Menu } = require('electron');
const path = require('path');
const fs = require('fs');
const ini = require('ini');
const sOS = require('os');
const { spawn } = require('child_process');
const sHttp = require('http');

const sPlataform = sOS.platform().toLowerCase();

app.disableHardwareAcceleration();

process.on('uncaughtException', (error) => {
    console.error('Exceção não tratada:', error);
});

const config = ini.parse(fs.readFileSync(path.join(app.getAppPath(), '/config/config.ini'), 'utf-8'));
const winOptions = {
    width: config.app.width,
    height: config.app.height,
    resizable: config.app.resizable,
    icon: path.join(app.getAppPath(), config.app.icon),
    webPreferences: {
        preload: path.join(app.getAppPath(), '/preload.js'),
    }
}

let sServerName;
let phpServerProcess;
let sPort;

function createMenu(sWin) {
    fs.readFile(path.join(app.getAppPath(), config.app.menu, '/menu.json'), (err, data) => {
        if (err) {
            console.error('Erro ao ler o arquivo JSON', err);
            return;
        }

        const menuData = JSON.parse(data);

        // Cria o menu principal
        const mainMenu = Menu.buildFromTemplate(getMenuTemplate(sWin, menuData));
        sWin.setMenu(mainMenu);
    });
}

const createWindow = () => {
    const win = new BrowserWindow(winOptions);
    win.setMenu(null);
    startPHPServer(win); // Inicie o servidor PHP

    if (config.app.menu) {
        createMenu(win);
    } else {
        win.setMenu(null);
    }

    if (config.dev.tools) {
        win.webContents.openDevTools();
    }

    win.webContents.setWindowOpenHandler(({ url }) => {
        if (url !== '') {
            miappNewWindow(url);

            return { action: 'deny' }
        }

        return { action: 'allow' }
    });

    app.on("browser-window-created", (e, win) => {
        if (config.dev.tools) {
            win.webContents.openDevTools();
        }

        if (!config.dev.menu) {
            win.removeMenu();
        }
    });

    const mifunctions = require(path.join(app.getAppPath(), '/mifunctions.js'));
    mifunctions.mifunctions(win);
}

// Aplica permissão de execução para o filephp
function permPHP(filephp) {
    spawn('chmod', ['+x', filephp]);
    config.php.perm = false;
    fs.writeFileSync(path.join(app.getAppPath(), '/config/config.ini'), ini.stringify(config));
}

// Inicia o servidor embutido do PHP
function startPHPServer(win) {
    let sFilePHP;
    let sFilePHPINI;

    if (sPlataform == 'linux') {
        if (config.php.folderphp) {
            sFilePHP = path.join(app.getAppPath(), '/php/', config.php.server);
        } else {
            sFilePHP = 'php';
        }

        if (config.php.perm) {
            permPHP(sFilePHP);
        }

        if (config.php.folderini || config.php.folderphp) {
            sFilePHPINI = path.join(app.getAppPath(), '/php/php.ini');
        } else {
            sFilePHPINI = '';
        }
    } else {
        app.quit();
    }

    let sCreateServer = sHttp.createServer();
    let sListen = sCreateServer.listen();
    sPort = sListen.address().port;
    sListen.close();
    sCreateServer.close();

    phpServerProcess = spawn(sFilePHP, ['-S', 'localhost:' + sPort, '-c', sFilePHPINI, '-t', path.join(app.getAppPath(), '/app/')], { detached: true });

    phpServerProcess.stderr.on('data', (data) => {
        const message = data.toString();

        if (message.includes('Development Server (http://localhost:' + sPort + ')')) {
            console.log('Servidor PHP iniciado com sucesso.');
            sServerName = `http://localhost:${sPort}/`;
            win.loadURL(sServerName);
        }
    });

    phpServerProcess.on('error', (err) => {
        console.error(`Erro ao iniciar o servidor PHP: ${err}`);
    });

    phpServerProcess.on('close', (code) => {
        console.log(`O servidor PHP foi encerrado com o código: ${code}`);
    });


    phpServerProcess.unref(); // Permite que o aplicativo seja fechado sem fechar o processo do servidor PHP
}

// Nova Janela
function miappNewWindow(url) {
    const sNewWindow = new BrowserWindow(winOptions);
    sNewWindow.setMenu(null);
    sNewWindow.loadURL(`${sServerName}/${url.replace(sServerName, '')}`);
    

    sNewWindow.webContents.setWindowOpenHandler(({ url }) => {
        if (url !== '') {
            console.log(url);
            miappNewWindow(`${url}`);

            return { action: 'deny' }
        }

        return { action: 'allow' }
    });

    createMenu(sNewWindow);
}

// Template de Menu
function getMenuTemplate(win, menuData) {
    const template = [];

    if (config.dev.menu) {
        const devMenu = {
            label: 'DevTools',
            submenu: [
                {
                    label: 'Refresh',
                    accelerator: 'F5',
                    click: () => {
                        win.reload();
                    }
                },
                {
                    label: 'Tools',
                    accelerator: 'F12',
                    click: () => {
                        win.openDevTools();
                    }
                }
            ]
        }

        template.push(devMenu);
    }

    // Loop sobre as chaves do objeto JSON
    Object.keys(menuData).forEach((key) => {
        const submenu = [];

        // Loop sobre os itens do submenu
        Object.keys(menuData[key]).forEach((submenuKey) => {
            const menuItem = {
                label: submenuKey,
                accelerator: menuData[key][submenuKey].key,
                click: () => {
                    // Verifica se é uma página ou URL
                    if (menuData[key][submenuKey].page) {
                        if (menuData[key][submenuKey].newwindow) {
                            miappNewWindow(menuData[key][submenuKey].page)
                            //win.webContents.executeJavaScript(`window.open('${menuData[key][submenuKey].page}', '_blank');`);
                        } else {
                            win.loadURL(sServerName + menuData[key][submenuKey].page);
                        }
                    } else if (menuData[key][submenuKey].url) {
                        require('electron').shell.openExternal(menuData[key][submenuKey].url);
                    }
                }
            };

            submenu.push(menuItem);
        });

        // Adiciona o submenu ao item do menu principal
        template.push({ label: key, submenu });
    });

    return template;
}

// Função para encerrar o processo com base na porta
function killProcessByPort(port) {
    let phpServerClose = spawn('lsof', ['-ti:' + port, '|', 'xargs', 'kill'], { shell: true });

    phpServerClose.stderr.on('data', (data) => {
        console.log(`Erro ao encerrar o processo na porta: ${sPort}`);
        return;
    });

    phpServerClose.on('error', (err) => {
        console.error(`Erro ao encerrar o processo na porta ${port}: ${err.message}`);
        return;
    });

    phpServerClose.on('close', (code) => {
        console.log(`O servidor PHP foi encerrado com o código: ${code}`);
        return;
    });

    console.log(`Processo na porta ${port} encerrado com sucesso.`);
}

function stopPHPServer() {
    if (phpServerProcess) {
        killProcessByPort(sPort); // Encerra todos os processos do PHP que estão sob a mesma porta
        console.log('Servidor PHP parado.');
    }
}

app.whenReady().then(() => {
    createWindow()

    // Enquanto os aplicativos do Linux e do Windows são encerrados quando não há janelas abertas, os aplicativos do macOS geralmente continuam em execução mesmo sem nenhuma janela aberta, e ativar o aplicativo quando não há janelas disponíveis deve abrir um novo.
    app.on('activate', () => {
        if (BrowserWindow.getAllWindows().length === 0) createWindow()
    })
})

// Para sair do aplicativo no Windows e Linux
// Se for MACOS não roda esse comando
app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') {
        stopPHPServer();
        app.quit();
    }
})

app.on('before-quit', () => {
    stopPHPServer();
});
