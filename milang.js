// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

module.exports = class milang {
    constructor(fs, path, app, valor) {

        let lang = JSON.parse(fs.readFileSync(path.join(app.getAppPath(), '/app/lang/lang.json'), 'utf-8'));
        this.sLangs = JSON.parse(fs.readFileSync(path.join(app.getAppPath(), '/app/lang/', `${lang[valor]}.json`), 'utf-8'));
    }

    traduzir(texto) {
        return (this.sLangs[texto]) ? this.sLangs[texto] : texto
    }
}