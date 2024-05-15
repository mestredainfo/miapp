// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

const { app, BrowserWindow } = require('electron');
const path = require('path');
const fs = require('fs');
const ini = require('ini');
const sOS = require('os');
const { exec, spawn } = require('child_process');
const sHttp = require('http');

const sPlataform = sOS.platform().toLowerCase();

app.disableHardwareAcceleration();

process.on('uncaughtException', (error) => {
    console.error('Exceção não tratada:', error);
});

let phpServerProcess;
let sPort;

const createWindow = () => {
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

    const win = new BrowserWindow(winOptions);

    win.setMenu(null);

    startPHPServer(win, config); // Inicie o servidor PHP

    if (config.app.devtools) {
        win.webContents.openDevTools();
    }

    win.webContents.setWindowOpenHandler(({ url }) => {
        if (url !== '') {
            return {
                action: 'allow',
                overrideBrowserWindowOptions: winOptions
            }
        }

        return { action: 'deny' }
    });

    app.on("browser-window-created", (e, win) => {
        if (config.app.devtools) {
            win.webContents.openDevTools();
        }
        win.removeMenu();
    });

    const mifunctions = require(path.join(app.getAppPath(), '/mifunctions.js'));
    mifunctions.mifunctions(win);
}

function permPHP(filephp, config) {
    spawn('chmod', ['+x', filephp]);
    config.php.perm = false;
    fs.writeFileSync(path.join(app.getAppPath(), '/config/config.ini'), ini.stringify(config));
}

function startPHPServer(win, config) {
    let sFilePHP;
    let sFilePHPINI;

    if (sPlataform == 'linux') {
        sFilePHP = path.join(app.getAppPath(), '/php/miappserver');

        if (config.php.perm) {
            permPHP(sFilePHP, config);
        }

        sFilePHPINI = path.join(app.getAppPath(), '/php/php.ini');
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
            win.loadURL('http://localhost:' + sPort);
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

// Função para encerrar o processo com base na porta
function killProcessByPort(port) {
    exec(`lsof -ti:${port} | xargs kill`, (error, stdout, stderr) => {
      if (error) {
        console.error(`Erro ao encerrar o processo na porta ${port}: ${error.message}`);
        return;
      }
      if (stderr) {
        console.error(`Erro ao encerrar o processo na porta ${port}: ${stderr}`);
        return;
      }
      console.log(`Processo na porta ${port} encerrado com sucesso.`);
    });
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
