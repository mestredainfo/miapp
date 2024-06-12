// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

const { app, BrowserWindow, Menu } = require('electron');
const path = require('path');
const fs = require('fs');
const sOS = require('os');
const { spawn } = require('child_process');
const sHttp = require('http');

const sPlataform = sOS.platform().toLowerCase();

// Argumentos
function getMIAppPath() {
    let aArg = process.argv;
    let sArg = aArg[1];
    if (sArg == '.') {
        sArg = aArg[2];
    }

    if (!sArg) {
        sArg = 'retorno';
        app.quit();
    }

    return sArg;
}

const miappPath = getMIAppPath();
const milangs = require(path.join(app.getAppPath(), '/milang.js'));
const milang = new milangs(sPlataform, app.getAppPath(), miappPath);

process.on('uncaughtException', (error) => {
    console.error(milang.miappTraduzir('Exceção não tratada:'), error);
});

const miupdates = require(path.join(app.getAppPath(), '/miupdate.js'));
const miupdate = new miupdates(milang);
miupdate.checkUpdate();

if (!fs.existsSync(path.join(getMIAppPath(), '/app/config/config.json'))) {
    console.log(milang.miappTraduzir('Não foi possível encontrar o arquivo %s', '"config.json"'));
    
    app.quit();
    return false;
}

const config = (miappPath !== 'retorno') ? JSON.parse(fs.readFileSync(path.join(getMIAppPath(), '/app/config/config.json'), 'utf-8')) : '';

if (miappPath !== 'retorno') {
    if (config.app.disableHardwareAcceleration) {
        app.disableHardwareAcceleration();
    }

    if (config.app.name) {
        app.setName(config.app.name);
    }
}

let sServerName;
let phpServerProcess;
let sPort;

function createMenu(sWin) {
    fs.readFile(path.join(getMIAppPath(), '/app/menu/menu.json'), (err, data) => {
        if (err) {
            console.error(milang.miappTraduzir('Erro ao ler o arquivo JSON'), err);
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
        icon: path.join(getMIAppPath(), '/app/icon/', config.app.icon),
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
    mifunctions.mifunctions(milang, miappNewWindow);
}

// Inicia o servidor embutido do PHP
function startPHPServer(win) {
    let sFilePHP;
    let sFilePHPINI;

    if (sPlataform == 'linux') {
        if (config.php.linux.custom) {
            if (config.php.linux.folder) {
                sFilePHP = path.join(app.getAppPath(), '/php/linux/', config.php.linux.custom);
            } else {
                sFilePHP = path.join(miappPath, '/app/php/', config.php.linux.custom);
            }
        } else {
            sFilePHP = path.join(app.getAppPath(), '/php/linux/miappserver');
        }

        if (config.php.linux.ini.custom) {
            if (config.php.linux.ini.folder) {
                sFilePHPINI = path.join(app.getAppPath(), '/php/linux/', config.php.linux.ini.custom);
            } else {
                sFilePHPINI = path.join(miappPath, '/app/php/', config.php.linux.ini.custom);
            }
        } else {
            sFilePHPINI = path.join(app.getAppPath(), '/php/linux/php.ini');

        }
    } else if (sPlataform == 'win32') {
        if (config.php.win32.custom) {
            if (config.php.win32.folder) {
                sFilePHP = path.join(app.getAppPath(), '/php/linux/', config.php.win32.custom);
            } else {
                sFilePHP = path.join(miappPath, '/app/php/', config.php.win32.custom);
            }
        } else {
            sFilePHP = path.join(app.getAppPath(), '/php/win32/php.exe');
        }

        if (config.php.win32.ini.custom) {
            if (config.php.win32.ini.folder) {
                sFilePHPINI = path.join(app.getAppPath(), '/php/linux/', config.php.win32.ini.custom);
            } else {
                sFilePHPINI = path.join(miappPath, '/app/php/', config.php.win32.ini.custom);
            }
        } else {
            sFilePHPINI = path.join(app.getAppPath(), '/php/win32/php.ini');

        }
    } else {
        app.quit();
    }

    // Environment
    process.env.miapppathroot = path.join(getMIAppPath(), '/app');
    process.env.miappusername = sOS.userInfo().username;
    process.env.miappuserpath = sOS.userInfo().homedir;
    process.env.miappplatform = sPlataform

    // Servidor
    let sCreateServer = sHttp.createServer();
    let sListen = sCreateServer.listen();
    sPort = sListen.address().port;
    sListen.close();
    sCreateServer.close();

    phpServerProcess = spawn(sFilePHP, ['-S', 'localhost:' + sPort, '-c', sFilePHPINI, '-t', path.join(getMIAppPath(), '/app/'), path.join(app.getAppPath(), '/app/router.php')], { cwd: process.env.HOME, env: process.env });

    phpServerProcess.on('error', (err) => {
        console.error(milang.miappTraduzir('Erro ao iniciar o servidor PHP:'), err);
    });

    phpServerProcess.on('close', (code) => {
        console.log(milang.miappTraduzir('O servidor PHP foi encerrado com o código:'), code);
    });

    if (sPlataform == 'linux') {
        const checkPortL = setInterval(() => {
            let lsof = spawn('lsof', ['-ti:' + sPort]);

            lsof.stdout.on('data', (data) => {
                console.log(milang.miappTraduzir('Servidor PHP iniciado com sucesso.'));
                sServerName = `http://localhost:${sPort}/`;
                win.loadURL(sServerName);
                clearInterval(checkPortL);
            });

            lsof.stderr.on('data', (data) => {
                console.error(milang.miappTraduzir('Erro ao executar lsof:'), data);
            });

            lsof.on('close', (code) => {
                if (code !== 0) {
                    console.error(milang.miappTraduzir('lsof saiu com código de erro'), code);
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
                console.error(milang.miappTraduzir('Erro ao executar netstat:'), data);
            });

            netstat.on('close', (code) => {
                if (code !== 0) {
                    console.error(milang.miappTraduzir('netstat saiu com código de erro'), code);
                }
                findstr.stdin.end();
            });

            findstr.stdout.on('data', (data) => {
                console.log(milang.miappTraduzir('Servidor PHP iniciado com sucesso.'));
                sServerName = `http://localhost:${sPort}/`;
                win.loadURL(sServerName);
                clearInterval(checkPortW);

            });
        }, 1000);
    }

    phpServerProcess.unref(); // Permite que o aplicativo seja fechado sem fechar o processo do servidor PHP
}

// Nova Janela
function miappNewWindow(url, width, height, resizable, menu, hide) {
    let sWidth = (width) ? width : config.app.width;
    let sHeight = (height) ? height : config.app.height;
    let sResizable = (resizable == true || resizable == false) ? resizable : config.app.resizable;
    let sMenu = (menu == true || menu == false) ? menu : config.app.menu;
    let sHide = (hide == true || hide == false) ? hide : false;

    const sNewWindow = new BrowserWindow({
        width: sWidth,
        height: sHeight,
        resizable: sResizable,
        icon: path.join(getMIAppPath(), '/app/icon/', config.app.icon),
        webPreferences: {
            preload: path.join(app.getAppPath(), '/preload.js'),
        }
    });

    if (sHide) {
        sNewWindow.hide();
    }

    sNewWindow.setMenu(null);
    sNewWindow.loadURL(`${sServerName}/${url.replace(sServerName, '')}`);

    sNewWindow.webContents.setWindowOpenHandler(({ url }) => {
        if (url !== '') {
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
            label: milang.miappTraduzir('Dev'),
            submenu: [
                {
                    label: milang.miappTraduzir('Refresh'),
                    accelerator: 'F5',
                    click: () => {
                        win.reload();
                    }
                },
                {
                    type: 'separator'
                },
                {
                    label: milang.miappTraduzir('DevTools'),
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
                    label: milang.traduzir(submenuKey),
                    accelerator: menuData[key][submenuKey].key,
                    click: () => {
                        // Verifica se é uma página ou URL
                        if (menuData[key][submenuKey].page) {
                            if (menuData[key][submenuKey].newwindow) {
                                miappNewWindow(menuData[key][submenuKey].page, menuData[key][submenuKey].width, menuData[key][submenuKey].height, menuData[key][submenuKey].resizable, menuData[key][submenuKey].menu, menuData[key][submenuKey].hide)
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
        template.push({ label: milang.traduzir(key), submenu });
    });

    return template;
}

// Função para encerrar o processo com base na porta
function killProcessByPort(port) {
    let phpServerClose;
    if (sPlataform == 'linux') {
        phpServerClose = spawn('lsof', ['-ti:' + port, '|', 'xargs', 'kill'], { shell: true });

        phpServerClose.stderr.on('data', (data) => {
            console.log(milang.miappTraduzir('Erro ao encerrar o processo na porta:'), sPort);
            return;
        });

        phpServerClose.on('error', (err) => {
            console.error(milang.miappTraduzir('Erro ao encerrar o processo na porta:'), port, err.message);
            return;
        });

        phpServerClose.on('close', (code) => {
            console.log(milang.miappTraduzir('O servidor PHP foi encerrado com o código:'), code);
            return;
        });

        console.log(milang.miappTraduzir('Processo na porta'), port, milang.traduzir('encerrado com sucesso.'));
    }
}

function stopPHPServer() {
    if (phpServerProcess) {
        killProcessByPort(sPort); // Encerra todos os processos do PHP que estão sob a mesma porta
        console.log(milang.miappTraduzir('Servidor PHP parado.'));
    }
}

app.whenReady().then(() => {
    if (miappPath !== 'retorno') {
        createWindow()

        // Enquanto os aplicativos do Linux e do Windows são encerrados quando não há janelas abertas, os aplicativos do macOS geralmente continuam em execução mesmo sem nenhuma janela aberta, e ativar o aplicativo quando não há janelas disponíveis deve abrir um novo.
        app.on('activate', () => {
            if (BrowserWindow.getAllWindows().length === 0) createWindow()
        })
    }
});

// Para sair do aplicativo no Windows e Linux
// Se for MACOS não roda esse comando
app.on('window-all-closed', () => {
    if (process.platform !== 'darwin') {
        stopPHPServer();
        app.quit();
    }
});

app.on('before-quit', () => {
    stopPHPServer();
});
