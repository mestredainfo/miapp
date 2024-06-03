<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

namespace MIApp;

class lang
{
    private array $sLang = [];

    public function __construct()
    {
        $aLang = file_get_contents(dirname(__FILE__, 3) . '/lang/lang.txt');
        $sPath = dirname(__FILE__) . '/lang/' . $aLang . '.json';

        if (file_exists($sPath)) {
            $this->sLang = json_decode(file_get_contents($sPath), true);
        } else {
            if (file_exists(dirname(__FILE__, 3) . '/lang/en.json')) {
                $this->sLang = json_decode(file_get_contents(dirname(__FILE__, 3) . '/lang/en.json'), true);
            } else {
                $this->sLang = [];
            }
        }
    }

    public function traduzir(string $texto): string
    {
        return (empty($this->sLang[$texto])) ? $texto : $this->sLang[$texto];
    }
}
