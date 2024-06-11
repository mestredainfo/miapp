<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

class miHTML
{
    /* Tags que não fecham */
    private array $naofecha = ['meta', 'input', 'br', 'link'];
    private array $atributosemvalor = ['required'];

    public function __call(string $element, mixed $arguments): string
    {
        /* Armazena o Conteudo */
        $sArgumento = '';
        $sAtributos = '';
        foreach ($arguments as $conteudo) {
            /* Verifica se é um atributo */
            if (is_array($conteudo)) {
                foreach ($conteudo as $atributo => $valor) {
                    /* Verifica se o atributo não tem valor */
                    if ($this->procurarValores($atributo, $this->atributosemvalor)) {
                        $sAtributos .= ' ' . $atributo;
                    } else {
                        $sAtributos .= ' ' . $atributo . '="' . $valor . '"';
                    }
                }
            } else {
                $sArgumento .= $conteudo; //. "\n";
            }
        }

        /* Procura se o elemento não fecha */
        if ($this->procurarValores($element, $this->naofecha) !== false) {
            /* Retorna esse código caso o elemento não possa ser fechado */
            return '<' . $element . $sAtributos . '>' . $sArgumento; //. "\n";
        } else {
            /* Retorna esse código caso o elemento possa ser fechado */
            return '<' . $element . $sAtributos . '>' . $sArgumento . '</' . $element . '>'; //. "\n";
        }
    }

    /* Condição if */
    public function if(mixed $condicao, mixed $callback): mixed
    {
        if ($condicao) {
            return $callback($this);
        }
    }

    /* Outras Condições */
    public function condicoes(mixed $callback): mixed
    {
        return $callback($this);
    }

    /* Retorna o DOCTYPE HTML5 */
    public function doctype(): string
    {
        return '<!DOCTYPE html>' . "\n";
    }

    /* Procura os Valores do Array */
    private function procurarValores(string $palavra, mixed $itens): bool
    {
        if (!is_array($itens)) {
            $aItens = array($itens);
        } else {
            $aItens = $itens;
        }

        foreach ($aItens as $valor) {
            if (strpos($palavra, $valor, 0) !== false) {
                /* Retorna o primeiro resultado e encerra a procura */
                return true;
            }
        }
        return false;
    }
}
