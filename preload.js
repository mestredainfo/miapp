// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: MIT

// Mestre da Info
// Site: https://linktr.ee/mestreinfo

const { contextBridge, ipcRenderer } = require('electron')

ipcRenderer.setMaxListeners(20);

// MIApp
contextBridge.exposeInMainWorld('miapp', {
    version: (type) => ipcRenderer.invoke('appVersao', type),
    alert: (title, msg, type, ...buttons) => ipcRenderer.invoke('appMessage', title, msg, type, ...buttons),
    confirm: (title, msg, type, ...buttons) => ipcRenderer.invoke('appConfirm', title, msg, type, ...buttons),
    newWindow: (url, width, height, resizable, frame, menu, hide) => ipcRenderer.invoke('appNewWindow', url, width, height, resizable, frame, menu, hide),
    openURL: (url) => ipcRenderer.invoke('appExterno', url),
    translate: (text, ...values) => ipcRenderer.invoke('appTraduzir', text, ...values),
    selectDirectory: () => ipcRenderer.invoke('selecionarDiretorio'),
    openFile: () => ipcRenderer.invoke('abrirArquivo'),
    saveFile: () => ipcRenderer.invoke('salvarArquivo'),
    notification: (title, text) => ipcRenderer.invoke('appNotification', title, text),
    tray: (title, tooltip, icon, menu) => ipcRenderer.invoke('appTray', title, tooltip, icon, menu),
    exportPDF: (filename, options) => ipcRenderer.invoke('appExportPDF', filename, options),
    exec: (command) => ipcRenderer.invoke('appExec', command),
    listExec: (listener) => ipcRenderer.on('list:exec', (event, ...args) => listener(...args) + ipcRenderer.removeListener('list:exec')),
});