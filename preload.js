// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

const { contextBridge, ipcRenderer } = require('electron')

ipcRenderer.setMaxListeners(20);

// MIApp
contextBridge.exposeInMainWorld('miapp', {
    version: (type) => ipcRenderer.invoke('appVersao', type),
    alert: (title, msg, type, confirm) => ipcRenderer.invoke('appMessage', title, msg, type, false),
    confirm: (title, msg, type, confirm) => ipcRenderer.invoke('appMessage', title, msg, type, true),
    newwindow: (url, width, height, resizable, menu, hide) => ipcRenderer.invoke('appNewWindow', url, width, height, resizable, menu, hide),
    openURL: (url) => ipcRenderer.invoke('appExterno', url),
    translate: (text, ...param) => ipcRenderer.invoke('appTraduzir', text, ...param),
    openFile: () => ipcRenderer.invoke('abrirArquivo'),
    saveFile: () => ipcRenderer.invoke('salvarArquivo'),
});
