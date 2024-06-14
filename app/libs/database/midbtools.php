<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

class miDBTools extends miDatabase
{
    public bool $ctInteger = false;
    public bool $ctNull = false;
    public bool $ctPrimaryKey = false;
    public bool $ctAutoIncrement = false;
    public string $ctDefaultValue = '';

    private array $ctColumn = [];

    public function integer()
    {
        $this->ctInteger = true;
    }

    public function null()
    {
        $this->ctNull = true;
    }

    public function primarykey()
    {
        $this->ctPrimaryKey = true;
    }

    public function autoincrement()
    {
        $this->ctAutoIncrement = true;
    }

    public function defaultvalue(string $value)
    {
        $this->ctDefaultValue = $value;
    }

    public function insertColumn(string $name)
    {
        $sql = $name;

        if ($this->ctInteger) {
            $sql .= ' INTEGER';
        } else {
            $sql .= ' TEXT';
        }

        if ($this->ctNull) {
            $sql .= ' NULL';
        } else {
            $sql .= ' NOT NULL';
        }

        if ($this->ctPrimaryKey) {
            $sql .= ' PRIMARY KEY';
        }

        if ($this->ctAutoIncrement) {
            $sql .= ' AUTOINCREMENT';
        }

        $this->ctColumn[] = $sql;

        $this->ctInteger = false;
        $this->ctNull = false;
        $this->ctPrimaryKey = false;
        $this->ctAutoIncrement = false;
        $this->ctDefaultValue = false;
    }

    public function create()
    {
        try {
            $sql = 'CREATE TABLE IF NOT EXISTS ';
            $sql .= $this->sTable[0] . ' (';
            $sql .= implode(',', $this->ctColumn);
            $sql .= ');';

            $this->sConecta->exec($sql);
        } catch (SQLite3Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function exec(string $sql)
    {
        $this->sConecta->exec($sql);
        $this->sPrepare = false;
        $this->sFechaResult = false;
    }
}
