<?php
// Copyright (C) 2004-2024 Murilo Gomes Julio
// SPDX-License-Identifier: GPL-2.0-only

// Organização: Mestre da Info
// Site: https://linktr.ee/mestreinfo

class miDBSelect extends miDatabase
{
    public function select()
    {
        try {
            $sql = 'SELECT ';

            if (empty($this->sColumns)) {
                $sql .= '*';
            } else {
                $sql .= implode(', ', $this->sColumns);
            }

            $sql .= ' FROM ' . implode(' INNER JOIN ', $this->sTable);
            $sql .= $this->getWhere();
            $sql .= $this->getOrder();
            $sql .= $this->getLimit();

            if ($this->sPrepare) {
                if ($this->sResult = $this->sConecta->prepare($sql)) {
                    $sIn = 1;
                    foreach ($this->sWhere as $row) {
                        if ($row['like']) {
                            $sLike = '%' . $row['valor'] . '%';
                            $this->sResult->bindParam(':' . $row['nome'], $sLike);
                        } else {
                            if ($row['in']) {
                                $this->sResult->bindParam(':' . $row['nome'] . $sIn, $row['valor']);
                                $sIn += 1;
                            } else {
                                $this->sResult->bindParam(':' . $row['nome'], $row['valor']);
                            }
                        }
                    }

                    $this->sData = $this->sResult->execute();
                    $this->sFechaResult = true;
                } else {
                    $this->sFechaResult = false;
                }
            } else {
                $this->sResult = $this->sConecta->query($sql);
                $this->sFechaResult = true;
            }
        } catch (SQLite3Exception $ex) {
            echo $ex->getMessage();
        }
    }

    public function execute()
    {
        $this->sData = $this->sResult->execute();
    }

    public function fetch(): array|false
    {
        if ($this->sPrepare) {
            return $this->sData->fetchArray(SQLITE3_ASSOC);
        } else {
            return $this->sResult->fetchArray(SQLITE3_ASSOC);
        }
    }

    public function rows(array $s)
    {
        $this->sRows = $s;
    }

    public function row(string $nome): mixed
    {
        return empty($this->sRows[$nome]) ? '' : $this->sRows[$nome];
    }
}
