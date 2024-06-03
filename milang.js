// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

module.exports = class milang {
    constructor(dirapp, valor) {
        const fs = require('fs');
        const path = require('path');
        let aLang = String(valor).substring(0, 2);

        let sPath = path.join(dirapp, '/app/lang/', `${aLang}.json`);
        if (fs.existsSync(sPath)) {
            this.sLang = JSON.parse(fs.readFileSync(sPath), 'utf-8');
        } else {
            this.sLang = JSON.parse(fs.readFileSync(path.join(dirapp, '/app/lang/pt1.json'), 'utf-8'));
        }
    }

    traduzir(texto) {
        return (this.sLang[texto]) ? this.sLang[texto] : texto
    }
}