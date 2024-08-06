// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: MIT

// Mestre da Info
// Site: https://linktr.ee/mestreinfo

const { app, BrowserWindow, Menu, MenuItem, dialog, ipcMain } = require('electron');
const path = require('path');
const fs = require('fs');
const sOS = require('os');
const { spawn } = require('child_process');
const sHttp = require('http');

const sPlataform = sOS.platform().toLowerCase();

const miAppPath = app.getAppPath().replace('app.asar', '');
const miAppSettings = JSON.parse(fs.readFileSync(path.join(miAppPath, '/config/settings.json'), 'utf-8'));
const miAppSettingApp = (miAppSettings['app']) ? '/app' : '/src';

// Lang
const getLang = () => {
    let aLang = '';

    if (sPlataform == 'linux') {
        aLang = (process.env.LANG || process.env.LANGUAGE || process.env.LC_ALL || process.env.LC_MESSAGES).split('.')[0].split('_')[0];
    } else {
        const { execFileSync } = require('child_process');
        let nLang = String(execFileSync('wmic', ['os', 'get', 'locale'])).trim();
        aLang = getNameLang(Number.parseInt(nLang.replace('Locale', ''), 16));
    }

    if (!fs.existsSync(path.join(miAppPath, miAppSettingApp, '/lang/en.json'))) {
        return false;
    }

    process.env.MIAPP_LANG = aLang;

    // Idioma do App
    let sPath = path.join(miAppPath, miAppSettingApp, '/lang/', `${aLang}.json`);

    if (fs.existsSync(sPath)) {
        return JSON.parse(fs.readFileSync(sPath), 'utf-8');
    } else {
        if (fs.existsSync(path.join(miAppPath, miAppSettingApp, '/lang/en.json'))) {
            return JSON.parse(fs.readFileSync(path.join(miAppPath, miAppSettingApp, '/lang/en.json'), 'utf-8'));
        } else {
            return [];
        }
    }
}

// Win32 
function getNameLang(valor) {
    let sCode = JSON.parse(fs.readFileSync(path.join(miAppPath, '/config/lang.json'), 'utf-8'));
    return (sCode[valor]) ? sCode[valor] : 'en';
}

const sLangApp = getLang();

if (!sLangApp) {
    console.log('File %s not found!', '"en.json"');
    app.quit();
    return false;
}

function traduzir(texto, ...values) {
    return (sLangApp[texto]) ? require('util').format(sLangApp[texto], ...values) : require('util').format(texto, ...values);
}

process.on('uncaughtException', (error) => {
    console.error(traduzir('Exceção não tratada:'), error);
});

if (!fs.existsSync(path.join(miAppPath, miAppSettingApp, '/config/config.json'))) {
    console.log(traduzir('Não foi possível encontrar o arquivo %s', '"config.json"'));

    app.quit();
    return false;
}

const config = JSON.parse(fs.readFileSync(path.join(miAppPath, miAppSettingApp, '/config/config.json'), 'utf-8'));

if (config.app.disableHardwareAcceleration) {
    app.disableHardwareAcceleration();
}

if (config.app.name) {
    app.setName(config.app.name);
}

let sServerName;
let phpServerProcess;
let sPort;

function createMenu(sWin, menus) {
    if (menus) {
        fs.readFile(path.join(miAppPath, miAppSettingApp, '/menu/menu.json'), (err, data) => {
            if (err) {
                console.error(traduzir('Erro ao ler o arquivo JSON'), err);
                return;
            }

            const menuData = JSON.parse(data);

            // Cria o menu principal
            const mainMenu = Menu.buildFromTemplate(getMenuTemplate(sWin, menuData, true));
            sWin.setMenu(mainMenu);
        });
    } else {
        // Cria o menu principal
        const mainMenu = Menu.buildFromTemplate(getMenuTemplate(sWin, '', true));
        sWin.setMenu(mainMenu);
    }
}

const createWindow = () => {
    const win = new BrowserWindow({
        width: config.app.width,
        height: config.app.height,
        resizable: config.app.resizable,
        frame: config.app.frame,
        icon: path.join(miAppPath, miAppSettingApp, '/icon/', config.app.icon),
        webPreferences: {
            preload: path.join(app.getAppPath(), '/preload.js'),
        }
    });
    win.setMenu(null);
    startPHPServer(win); // Inicie o servidor PHP

    if (config.app.menu) {
        createMenu(win, true);
    } else {
        createMenu(win);
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

    createMenuContext(win);

    // Functions
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
        let sButtons = [traduzir('Continuar'), ...buttons];

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
        let sButtons = [traduzir('Continuar'), traduzir('Cancelar'), ...buttons];

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
        return traduzir(text, ...values);
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
                console.log(traduzir('PDF salvo com sucesso em %s', pdfPath))
            })
        }).catch(error => {
            console.log(traduzir('Erro ao tentar gerar o PDF em %s', pdfPath), error)
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

// Aplica permissão de execução para o filephp
function permPHP(filephp) {
    if (config.php.linux.perm) {
        spawn('chmod', ['+x', filephp]);
        config.php.linux.perm = false;

        fs.writeFileSync(path.join(miAppPath, miAppSettingApp, '/config/config.json'), JSON.stringify(config, '', "\t"));

        console.log(traduzir('Aplicado permissão de execução para o %s', path.basename(filephp)));
    }
}

// Inicia o servidor embutido do PHP
function startPHPServer(win) {
    let sFilePHP;
    let sFilePHPINI;

    if (sPlataform == 'linux') {
        if (config.php.linux.custom) {
            if (config.php.linux.folder) {
                sFilePHP = path.join(miAppPath, '/php/linux/', config.php.linux.custom);
                permPHP(sFilePHP);
            } else {
                sFilePHP = config.php.linux.custom;
            }
        } else {
            sFilePHP = path.join(miAppPath, '/php/linux/miappserver');
            permPHP(sFilePHP);
        }

        if (config.php.linux.ini.custom) {
            if (config.php.linux.ini.folder) {
                sFilePHPINI = path.join(miAppPath, '/php/linux/', config.php.linux.ini.custom);
            } else {
                sFilePHPINI = config.php.linux.ini.custom;
            }
        } else {
            sFilePHPINI = path.join(miAppPath, '/php/linux/php.ini');

        }
    } else if (sPlataform == 'win32') {
        if (config.php.win32.custom) {
            if (config.php.win32.folder) {
                sFilePHP = path.join(miAppPath, '/php/win32/', config.php.win32.custom);
            } else {
                sFilePHP = config.php.win32.custom;
            }
        } else {
            sFilePHP = path.join(miAppPath, '/php/win32/php.exe');
        }

        if (config.php.win32.ini.custom) {
            if (config.php.win32.ini.folder) {
                sFilePHPINI = path.join(miAppPath, '/php/win32/', config.php.win32.ini.custom);
            } else {
                sFilePHPINI = config.php.win32.ini.custom;
            }
        } else {
            sFilePHPINI = path.join(miAppPath, '/php/win32/php.ini');

        }
    } else {
        app.quit();
    }

    // Environment
    process.env.MIAPP_USERNAME = sOS.userInfo().username;
    process.env.MIAPP_USERPATH = sOS.userInfo().homedir;
    process.env.MIAPP_PLATFORM = sPlataform
    process.env.MIAPP_PATH = path.join(miAppPath, miAppSettingApp, '/');

    let sArgs = process.argv;
    let sArgv = '';
    if (sArgs[1] == '.') {
        sArgv = sArgs.slice(2).toString();
    } else {
        sArgv = sArgs.slice(1).toString();
    }
    process.env.MIAPP_ARGV = sArgv;

    // Servidor
    let sCreateServer = sHttp.createServer();
    let sListen = sCreateServer.listen();
    sPort = sListen.address().port;
    sListen.close();
    sCreateServer.close();

    // Router
    if (config.php.router) {
        let sRouter = '';
        sRouter = path.join(miAppPath, miAppSettingApp, '/router.php');
        phpServerProcess = spawn(sFilePHP, ['-S', 'localhost:' + sPort, '-c', sFilePHPINI, '-t', path.join(miAppPath, miAppSettingApp, '/'), sRouter], { cwd: process.env.HOME, env: process.env });
    } else {
        phpServerProcess = spawn(sFilePHP, ['-S', 'localhost:' + sPort, '-c', sFilePHPINI, '-t', path.join(miAppPath, miAppSettingApp, '/')], { cwd: process.env.HOME, env: process.env });
    }

    phpServerProcess.on('error', (err) => {
        console.error(traduzir('Erro ao iniciar o servidor PHP:'), err);
    });

    phpServerProcess.on('close', (code) => {
        console.log(traduzir('O servidor PHP foi encerrado com o código:'), code);
    });

    if (sPlataform == 'linux') {
        const checkPortL = setInterval(() => {
            let lsof = spawn('lsof', ['-ti:' + sPort]);

            lsof.stdout.on('data', (data) => {
                console.log(traduzir('Servidor PHP iniciado com sucesso.'));
                sServerName = `http://localhost:${sPort}/`;
                win.loadURL(sServerName);
                clearInterval(checkPortL);
            });

            lsof.stderr.on('data', (data) => {
                console.error(traduzir('Erro ao executar lsof:'), data);
            });

            lsof.on('close', (code) => {
                if (code !== 0) {
                    console.error(traduzir('lsof saiu com código de erro'), code);
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
                console.error(traduzir('Erro ao executar netstat:'), data);
            });

            netstat.on('close', (code) => {
                if (code !== 0) {
                    console.error(traduzir('netstat saiu com código de erro'), code);
                }
                findstr.stdin.end();
            });

            findstr.stdout.on('data', (data) => {
                console.log(traduzir('Servidor PHP iniciado com sucesso.'));
                sServerName = `http://localhost:${sPort}/`;
                win.loadURL(sServerName);
                clearInterval(checkPortW);

            });
        }, 1000);
    }

    phpServerProcess.unref(); // Permite que o aplicativo seja fechado sem fechar o processo do servidor PHP
}

// Nova Janela
function miappNewWindow(url, width, height, resizable, frame, menu, hide) {
    let sWidth = (width) ? width : config.app.width;
    let sHeight = (height) ? height : config.app.height;
    let sResizable = (resizable == true || resizable == false) ? resizable : config.app.resizable;
    let sFrame = (frame == true || frame == false) ? frame : config.app.frame;
    let sMenu = (menu == true || menu == false) ? menu : config.app.menu;
    let sHide = (hide == true || hide == false) ? hide : false;

    const sNewWindow = new BrowserWindow({
        width: sWidth,
        height: sHeight,
        resizable: sResizable,
        frame: sFrame,
        icon: path.join(miAppPath, miAppSettingApp, '/icon/', config.app.icon),
        webPreferences: {
            preload: path.join(app.getAppPath(), '/preload.js'),
        }
    });

    if (sHide) {
        sNewWindow.hide();
    }

    sNewWindow.setMenu(null);
    sNewWindow.loadURL(`${sServerName}/${url.replace(sServerName, '')}`);

    createMenuContext(sNewWindow);

    sNewWindow.webContents.setWindowOpenHandler(({ url }) => {
        if (url !== '') {
            miappNewWindow(`${url}`);

            return { action: 'deny' }
        }

        return { action: 'allow' }
    });

    if (sMenu) {
        createMenu(sNewWindow, true);
    }
}

// Template de Menu
function getMenuTemplate(win, menuData, menus) {
    let template = [];

    if (menus) {
        if (config.dev.menu) {
            let devMenu = {
                label: traduzir('Dev'),
                submenu: [
                    {
                        label: traduzir('Atualizar'),
                        accelerator: 'F5',
                        click: () => {
                            win.reload();
                        }
                    },
                    {
                        type: 'separator'
                    },
                    {
                        label: traduzir('Ferramentas do Desenvolvedor'),
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
                        label: traduzir(submenuKey),
                        accelerator: menuData[key][submenuKey].key,
                        click: () => {
                            // Verifica se é uma página ou URL
                            if (menuData[key][submenuKey].page) {
                                if (menuData[key][submenuKey].newwindow) {
                                    miappNewWindow(menuData[key][submenuKey].page, menuData[key][submenuKey].width, menuData[key][submenuKey].height, menuData[key][submenuKey].resizable, menuData[key][submenuKey].frame, menuData[key][submenuKey].menu, menuData[key][submenuKey].hide)
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
            template.push({ label: traduzir(key), submenu });
        });
    }

    return template;
}

function createMenuContext(win) {
    const contextMenu = new Menu();
    contextMenu.append(new MenuItem({
        label: traduzir('Recortar'),
        role: 'cut'
    }));
    contextMenu.append(new MenuItem({
        label: traduzir('Copiar'),
        role: 'copy'
    }));
    contextMenu.append(new MenuItem({
        label: traduzir('Colar'),
        role: 'paste'
    }));
    contextMenu.append(new MenuItem({
        type: "separator"
    }));
    contextMenu.append(new MenuItem({
        label: traduzir('Selecionar Tudo'),
        role: 'selectall'
    }));

    win.webContents.on('context-menu', (event, params) => {
        if (params.formControlType == 'input-text' || params.formControlType == 'text-area') {
            contextMenu.popup({
                window: win,
                x: params.x,
                y: params.y
            });
        }
    });
}

// Função para encerrar o processo com base na porta
function killProcessByPort(port) {
    let phpServerClose;
    if (sPlataform == 'linux') {
        phpServerClose = spawn('lsof', ['-ti:' + port, '|', 'xargs', 'kill'], { shell: true });

        phpServerClose.stderr.on('data', (data) => {
            console.log(traduzir('Erro ao encerrar o processo na porta:'), sPort);
            return;
        });

        phpServerClose.on('error', (err) => {
            console.error(traduzir('Erro ao encerrar o processo na porta:'), port, err.message);
            return;
        });

        phpServerClose.on('close', (code) => {
            console.log(traduzir('O servidor PHP foi encerrado com o código:'), code);
            return;
        });

        console.log(traduzir('Processo na porta'), port, traduzir('encerrado com sucesso.'));
    }
}

function stopPHPServer() {
    if (phpServerProcess) {
        killProcessByPort(sPort); // Encerra todos os processos do PHP que estão sob a mesma porta
        console.log(traduzir('Servidor PHP parado.'));
    }
}

app.whenReady().then(() => {
    createWindow()

    // Enquanto os aplicativos do Linux e do Windows são encerrados quando não há janelas abertas, os aplicativos do macOS geralmente continuam em execução mesmo sem nenhuma janela aberta, e ativar o aplicativo quando não há janelas disponíveis deve abrir um novo.
    app.on('activate', () => {
        if (BrowserWindow.getAllWindows().length === 0) createWindow()
    });
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
