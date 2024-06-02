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

process.on('uncaughtException', (error) => {
    console.error('Exceção não tratada:', error);
});

const config = ini.parse(fs.readFileSync(path.join(app.getAppPath(), '/config/config.ini'), 'utf-8'));

if (config.app.disableHardwareAcceleration) {
    app.disableHardwareAcceleration();
}

if (config.app.name) {
    app.setName(config.app.name);
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
    const win = new BrowserWindow({
        width: config.app.width,
        height: config.app.height,
        resizable: config.app.resizable,
        icon: path.join(app.getAppPath(), config.app.icon),
        webPreferences: {
            preload: path.join(app.getAppPath(), '/preload.js'),
        }
    });
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
    mifunctions.mifunctions(win, miappNewWindow);
}

// Aplica permissão de execução para o filephp
function permPHP(filephp) {
    spawn('chmod', ['+x', filephp]);
    config.phplinux.perm = false;

    let sTopoINI = '# Copyright (C) 2004-2024 Murilo Gomes Julio\n';
    sTopoINI += '# SPDX-License-Identifier: GPL-2.0-only\n\n';
    sTopoINI += '# Organização: Mestre da Info\n';
    sTopoINI += '# Site: https://linktr.ee/mestreinfo\n\n';

    fs.writeFileSync(path.join(app.getAppPath(), '/config/config.ini'), sTopoINI + ini.stringify(config));
}

// Inicia o servidor embutido do PHP
function startPHPServer(win) {
    let sFilePHP;
    let sFilePHPINI;

    if (sPlataform == 'linux') {
        if (config.phplinux.customphp) {
            sFilePHP = config.phplinux.customphp
        } else {
            sFilePHP = path.join(app.getAppPath(), '/php/linux/miappserver');
        }

        if (config.phplinux.perm) {
            permPHP(sFilePHP);
        }

        if (config.phplinux.customini) {
            sFilePHPINI = config.phplinux.customini
        } else {
            sFilePHPINI = path.join(app.getAppPath(), '/php/linux/php.ini');

        }
    } else if (sPlataform == 'win32') {
        if (config.phpwin32.customphp) {
            sFilePHP = config.phpwin32.customphp
        } else {
            sFilePHP = path.join(app.getAppPath(), '/php/win32/php.exe');
        }

        if (config.phpwin32.customini) {
            sFilePHPINI = config.phpwin32.customini
        } else {
            sFilePHPINI = path.join(app.getAppPath(), '/php/win32/php.ini');

        }
    } else {
        app.quit();
    }

    let sCreateServer = sHttp.createServer();
    let sListen = sCreateServer.listen();
    sPort = sListen.address().port;
    sListen.close();
    sCreateServer.close();

    phpServerProcess = spawn(sFilePHP, ['-S', 'localhost:' + sPort, '-c', sFilePHPINI, '-t', path.join(app.getAppPath(), '/app/')], { cwd: process.env.HOME, env: process.env });

    phpServerProcess.on('error', (err) => {
        console.error(`Erro ao iniciar o servidor PHP: ${err}`);
    });

    phpServerProcess.on('close', (code) => {
        console.log(`O servidor PHP foi encerrado com o código: ${code}`);
    });

    if (sPlataform == 'linux') {
        const checkPortL = setInterval(() => {
            let lsof = spawn('lsof', ['-ti:' + sPort]);

            lsof.stdout.on('data', (data) => {
                console.log('Servidor PHP iniciado com sucesso.');
                sServerName = `http://localhost:${sPort}/`;
                win.loadURL(sServerName);
                clearInterval(checkPortL);
            });

            lsof.stderr.on('data', (data) => {
                console.error(`Erro ao executar lsof: ${data}`);
            });

            lsof.on('close', (code) => {
                if (code !== 0) {
                    console.error(`lsof saiu com código de erro ${code}`);
                }
            });
        }, 1000);
    } else if (sPlataform == 'win32') {
        const checkPortW = setInterval(() => {
            let netstat = spawn('netstat', ['-ano']);
            let findstr = spawn('findstr', [':' + sPort]);

            netstat.stdout.on('data', (data) => {
                findstr.stdin.write(data);
            });

            netstat.stderr.on('data', (data) => {
                console.error(`Erro ao executar netstat: ${data}`);
            });

            netstat.on('close', (code) => {
                if (code !== 0) {
                    console.error(`netstat saiu com código de erro ${code}`);
                }
                findstr.stdin.end();
            });

            findstr.stdout.on('data', (data) => {
                console.log('Servidor PHP iniciado com sucesso.');
                sServerName = `http://localhost:${sPort}/`;
                win.loadURL(sServerName);
                clearInterval(checkPortW);

            });
        }, 1000);
    }

    phpServerProcess.unref(); // Permite que o aplicativo seja fechado sem fechar o processo do servidor PHP
}

// Nova Janela
function miappNewWindow(url, width, height, resizable, menu) {
    let sWidth = (width) ? width : config.app.width;
    let sHeight = (height) ? height : config.app.height;
    let sResizable = (resizable == true || resizable == false) ? resizable : config.app.resizable;
    let sMenu = (menu == true || menu == false) ? menu : config.app.menu;

    const sNewWindow = new BrowserWindow({
        width: sWidth,
        height: sHeight,
        resizable: sResizable,
        icon: path.join(app.getAppPath(), config.app.icon),
        webPreferences: {
            preload: path.join(app.getAppPath(), '/preload.js'),
        }
    });
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

    if (sMenu) {
        createMenu(sNewWindow);
    }
}

// Template de Menu
function getMenuTemplate(win, menuData) {
    let template = [];

    if (config.dev.menu) {
        let devMenu = {
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
                    type: 'separator'
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
        let submenu = [];

        // Loop sobre os itens do submenu
        Object.keys(menuData[key]).forEach((submenuKey) => {
            let menuItem = {};

            if (submenuKey.indexOf('separator') == 0) {
                menuItem = { type: 'separator' };
            } else {
                menuItem = {
                    label: submenuKey,
                    accelerator: menuData[key][submenuKey].key,
                    click: () => {
                        // Verifica se é uma página ou URL
                        if (menuData[key][submenuKey].page) {
                            if (menuData[key][submenuKey].newwindow) {
                                miappNewWindow(menuData[key][submenuKey].page, menuData[key][submenuKey].width, menuData[key][submenuKey].height, menuData[key][submenuKey].resizable, menuData[key][submenuKey].menu)
                            } else {
                                win.loadURL(sServerName + menuData[key][submenuKey].page);
                            }
                        } else if (menuData[key][submenuKey].url) {
                            require('electron').shell.openExternal(menuData[key][submenuKey].url);
                        } else if (menuData[key][submenuKey].script) {
                            win.webContents.executeJavaScript(menuData[key][submenuKey].script);
                        }
                    }
                };
            }

            submenu.push(menuItem);
        });

        // Adiciona o submenu ao item do menu principal
        template.push({ label: key, submenu });
    });

    return template;
}

// Função para encerrar o processo com base na porta
function killProcessByPort(port) {
    let phpServerClose;
    if (sPlataform == 'linux') {
        phpServerClose = spawn('lsof', ['-ti:' + port, '|', 'xargs', 'kill'], { shell: true });

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
