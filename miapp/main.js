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

const config = ini.parse(fs.readFileSync(path.join(app.getAppPath(), '/config/config.ini'), 'utf-8'));

if (config.app.disableHardwareAcceleration) {
    app.disableHardwareAcceleration();
}

process.on('uncaughtException', (error) => {
    console.error('Exceção não tratada:', error);
});

const winOptions = {
    width: config.app.width,
    height: config.app.height,
    resizable: true,
    icon: path.join(app.getAppPath(), '/icon/miapp.png'),
    webPreferences: {
        preload: path.join(app.getAppPath(), '/preload.js'),
    }
}

let sServerName;
let phpServerProcess;
let sPort;

const createWindow = () => {
    const win = new BrowserWindow(winOptions);
    win.setMenu(null);
    startPHPServer(win); // Inicie o servidor PHP

    win.webContents.setWindowOpenHandler(({ url }) => {
        if (url !== '') {
            miappNewWindow(url);

            return { action: 'deny' }
        }

        return { action: 'allow' }
    });

    app.on("browser-window-created", (e, win) => {
        win.removeMenu();
    });

    const mifunctions = require(path.join(app.getAppPath(), '/mifunctions.js'));
    mifunctions.mifunctions(win);
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

// Argumentos
function checkArg(nome) {
    let sArg = process.argv.findIndex(arg => arg.startsWith(`--${nome}=`))
    return (sArg !== -1) ? true : false;
}

function getArg(nome) {
    let sArg = process.argv.findIndex(arg => arg.startsWith(`--${nome}=`))
    return process.argv[sArg].split('=')[1]
}

// Inicia o servidor embutido do PHP
function startPHPServer(win) {
    let sFilePHP;
    let sFilePHPINI;

    if (sPlataform == 'linux') {
        sFilePHP = path.join(app.getAppPath(), '/php/linux/miappserver');
        sFilePHPINI = path.join(app.getAppPath(), '/php/linux/php.ini');

        if (config.phplinux.perm) {
            permPHP(sFilePHP);
        }
    } else if (sPlataform == 'win32') {
        sFilePHP = path.join(app.getAppPath(), '/php/win32/php.exe');
        sFilePHPINI = path.join(app.getAppPath(), '/php/win32/php.ini');
    } else {
        app.quit();
    }

    let sFolderProject = (checkArg('app')) ? path.join(path.resolve(__dirname, '..'), '/apps/', getArg('app')) : path.join(path.resolve(__dirname, '..'), '/apps/');

    let sCreateServer = sHttp.createServer();
    let sListen = sCreateServer.listen();
    sPort = sListen.address().port;
    sListen.close();
    sCreateServer.close();

    phpServerProcess = spawn(sFilePHP, ['-S', 'localhost:' + sPort, '-c', sFilePHPINI, '-t', sFolderProject], { cwd: process.env.HOME, env: process.env });

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
