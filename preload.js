// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

const { contextBridge, ipcRenderer } = require('electron')

ipcRenderer.setMaxListeners(20);

// Abrir, salvar e criar arquivo
contextBridge.exposeInMainWorld('arquivo', {
    abrir: () => ipcRenderer.invoke('abrirArquivo'),
    salvar: () => ipcRenderer.invoke('salvarArquivo'),
});

// Abrir aplicativo externo
contextBridge.exposeInMainWorld('externo', {
    rodar: (url) => ipcRenderer.invoke('appExterno', url)
});

// MIApp
contextBridge.exposeInMainWorld('miapp', {
    versao: (tipo) => ipcRenderer.invoke('appVersao', tipo),
    message: (title, msg, type) => ipcRenderer.invoke('appMessage', title, msg, type),
    confirm: (title, msg, type) => ipcRenderer.invoke('appConfirm', title, msg, type)
});