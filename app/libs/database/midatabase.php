<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organization: Mestre da Info
// Site: https://linktr.ee/mestreinfo

class miDatabase
{
    protected mixed $sConecta;
    protected mixed $sResult;
    protected mixed $sData;

    protected bool $sPrepare = false;
    protected bool $sFechaResult = true;

    // Select
    protected array $sColumns = [];

    // Select, Insert, Update e Delete
    protected array $sTable = [];

    // Insert e Update
    protected array $sValores = [];

    // Where para Select, Update e Delete
    protected array $sWhere = [];
    private bool $sWhereColumn = false;
    private bool $sAnd = true;
    private bool $sLike = false;
    private bool $sIn = false;

    protected array $sOrders = [];
    private bool $sAsc = true;

    protected string $sLimit = '';

    // Select
    protected array $sRows = [];

    public const sqlite3ReadWrite = SQLITE3_OPEN_READWRITE;
    public const sqlite3Create = SQLITE3_OPEN_CREATE;
    public const sqlite3ReadOnly = SQLITE3_OPEN_READONLY;

    public function __construct(array $db, int $options = SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE)
    {
        try {
            $this->sConecta = new SQLite3($db['database'], $options);
        } catch (SQLite3Exception $ex) {
            $ex->getMessage();
            exit;
        }
    }

    public function ativarPrepare()
    {
        $this->sPrepare = true;
        return $this;
    }

    public function column(string $nome)
    {
        $this->sColumns[] = $nome;
        return $this;
    }

    public function table(string $nome)
    {
        $this->sTable[] = $nome;
        return $this;
    }

    public function or()
    {
        $this->sAnd = false;
        return $this;
    }

    public function like()
    {
        $this->sLike = true;
        return $this;
    }

    public function wherecolumn()
    {
        $this->sWhereColumn = true;
        return $this;
    }

    public function in()
    {
        $this->sIn = true;
        return $this;
    }

    public function where(string $nome, string|int $valor)
    {
        $sAndOr = ' AND ';
        if (!$this->sAnd) {
            $sAndOr = ' OR ';
        }

        $this->sWhere[] = [
            'nome' => $nome,
            'valor' => $valor,
            'andor' => $sAndOr,
            'like' => $this->sLike,
            'column' => $this->sWhereColumn,
            'in' => $this->sIn
        ];

        $this->sAnd = true;
        $this->sLike = false;
        $this->sWhereColumn = false;
        $this->sIn = false;

        return $this;
    }

    public function desc()
    {
        $this->sAsc = false;
        return $this;
    }

    public function order(string $nome)
    {
        $sAscDesc = ' ASC ';
        if (!$this->sAsc) {
            $sAscDesc = ' DESC ';
        }

        $this->sOrders[] = [
            'nome' => $nome,
            'ordem' => $sAscDesc
        ];

        $this->sAsc = true;

        return $this;
    }

    public function limit(int $c, int $q)
    {
        $this->sLike = $c . ',' . $q;
        return $this;
    }

    public function insertValue(string $nome, string $valor)
    {
        $sAndOr = ' AND ';
        if (!$this->sAnd) {
            $sAndOr = ' OR ';
        }

        $this->sValores[] = [
            'nome' => $nome,
            'valor' => $valor,
            'andor' => $sAndOr
        ];

        $this->sAnd = true;
        $this->sLike = false;

        return $this;
    }

    protected function getWhere(): string
    {
        $sql = '';

        if (!empty($this->sWhere)) {
            $sql .= ' WHERE ';

            $sIn = 1;
            foreach ($this->sWhere as $row) {
                if (empty($row['column'])) {
                    if ($this->sPrepare) {
                        if (empty($row['like'])) {
                            if ($row['in']) {
                                $sql .= $row['nome'] . '=:' . $row['nome'] . $sIn . $row['andor'];
                                $sIn += 1;
                            } else {
                                $sql .= $row['nome'] . '=:' . $row['nome'] . $row['andor'];
                            }
                        } else {
                            $sql .= $row['nome'] . ' LIKE :' . $row['nome'] . $row['andor'];
                        }
                    } else {
                        if (empty($row['like'])) {
                            if (is_int($row['valor'])) {
                                $sql .= $row['nome'] . '=' . $row['valor'] . $row['andor'];
                            } else {
                                $sql .= $row['nome'] . '="' . $row['valor'] . '"' . $row['andor'];
                            }
                        } else {
                            if (is_int($row['valor'])) {
                                $sql .= $row['nome'] . ' LIKE ' . $row['valor'] . $row['andor'];
                            } else {
                                $sql .= $row['nome'] . ' LIKE "%' . $row['valor'] . '%"' . $row['andor'];
                            }
                        }
                    }
                } else {
                    if (empty($row['like'])) {
                        $sql .= $row['nome'] . '=' . $row['value'] . $row['andor'];
                    } else {
                        $sql .= $row['nome'] . ' LIKE ' . $row['value'] . $row['andor'];
                    }
                }   
            }

            $sql = rtrim($sql, ' AND ');
            $sql = rtrim($sql, ' OR ');
        }

        return $sql;
    }

    protected function getOrder(): string
    {
        $sql = '';
        if (!empty($this->sOrders)) {
            $sql .= ' ORDER BY ';
            foreach ($this->sOrders as $row) {
                $sql .= $row['nome'] . ' ' . $row['ordem'] . ',';
            }

            $sql = rtrim($sql, ',');
        }
        return $sql;
    }

    public function getLimit(): string
    {
        $sql = '';
        if (!empty($this->sLimit)) {
            $sql .= ' LIMIT ' . $this->sLimit;
        }
        return $sql;
    }

    public function close()
    {
        if ($this->sFechaResult) {
            if ($this->sPrepare) {
                $this->sData = null;
                $this->sResult->close();
            } else {
                $this->sResult = null;
            }
        }

        $this->sConecta->close();
    }
}
