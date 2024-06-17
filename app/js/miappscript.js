// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

let miappmenucheck = true;
try {
    const miappmenu = document.getElementById('miappmenu');
    miappinit.menu((data) => {
        if (!data) {
            sessionStorage.setItem("miappmenu", "nodisplay");
            miappmenu.style.display = "none";
            miappmenucheck = false;
        }
    });
} catch (ex) {
    //
}

if (miappmenucheck) {
    if (sessionStorage.getItem("miappmenu") == 'nodisplay') {
        miappmenu.style.display = "none";
    }
}

// Atalho de Menu
// Adiciona evento de keydown ao documento
document.addEventListener('keydown', function (e) {
    // Obtém todos os links dentro do menu
    const links = document.querySelectorAll('#miappmenu a');

    // Itera sobre cada link para verificar a tecla de atalho
    links.forEach(function (link) {
        // Obtém a tecla de atalho associada ao link
        const shortcut = link.getAttribute('data-key');

        // Verifica se a tecla pressionada corresponde à tecla de atalho do link
        if (shortcut && e.key === shortcut) {
            // Redireciona para o URL do link
            window.location.href = link.href;
        }
    });
});

// Função para exibir o menu de contexto
function miappshowContextMenu(x, y) {
    const contextMenu = document.getElementById('miAppContextMenu');
    contextMenu.style.display = 'block';
    contextMenu.style.left = x + 'px';
    contextMenu.style.top = y + 'px';
}

// Função para ocultar o menu de contexto
function miapphideContextMenu() {
    const contextMenu = document.getElementById('miAppContextMenu');
    contextMenu.style.display = 'none';
}

let miappMenuContextElement;
// Adiciona evento de contexto ao documento inteiro
document.addEventListener('contextmenu', function (e) {
    e.preventDefault(); // Impede o menu de contexto padrão do navegador

    // Verifica se o elemento clicado é um input ou textarea
    if (e.target.nodeName === 'INPUT' || e.target.nodeName === 'TEXTAREA') {
        miappMenuContextElement = e.target;
        miappshowContextMenu(e.pageX, e.pageY);
    } else {
        miapphideContextMenu(); // Oculta o menu se não for input ou textarea
    }
});

// Ações do menu de contexto
document.getElementById('miappCutAction').addEventListener('click', async function () {
    const target = miappMenuContextElement
    if (target && (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA')) {
        await navigator.clipboard.writeText(target.value.substring(target.selectionStart, target.selectionEnd));
        target.setRangeText('', target.selectionStart, target.selectionEnd, 'end');
    }
    miapphideContextMenu();
});

document.getElementById('miappCopyAction').addEventListener('click', async function (e) {
    const target = miappMenuContextElement
    if (target && (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA')) {
        try {
            await navigator.clipboard.writeText(target.value.substring(target.selectionStart, target.selectionEnd));
            console.log('Texto copiado para a área de transferência');
        } catch (err) {
            console.error('Falha ao copiar texto: ', err);
        }
    }
    miapphideContextMenu();
});

document.getElementById('miappPasteAction').addEventListener('click', async function () {
    const target = miappMenuContextElement
    if (target && (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA')) {
        try {
            const clipText = await navigator.clipboard.readText();
            const startPosition = target.selectionStart;
            const endPosition = target.selectionEnd;
            const newText = target.value.substring(0, startPosition) + clipText + target.value.substring(endPosition);
            target.value = newText;
            target.setSelectionRange(startPosition + clipText.length, startPosition + clipText.length);
        } catch (err) {
            console.error('Falha ao colar texto: ', err);
        }
    }
    miapphideContextMenu();
});

document.getElementById('miappSelectAllAction').addEventListener('click', function () {
    const target = miappMenuContextElement
    if (target && (target.tagName === 'INPUT' || target.tagName === 'TEXTAREA')) {
        target.select();
    }
    miapphideContextMenu();
});

// Oculta o menu de contexto ao clicar fora dele
document.addEventListener('click', function (e) {
    if (!document.getElementById('miAppContextMenu').contains(e.target)) {
        miapphideContextMenu();
    }
});