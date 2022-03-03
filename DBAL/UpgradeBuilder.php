<?php

namespace common\db\DBAL;

class UpgradeBuilder extends RemoveBuilder
{
    private string $set = '';

    /**
     * @param string $set
     * @return $this
     */
    public function set(string $set): self
    {
        $this->set = $set;
        return $this;
    }

    /**
     * @return Result
     */
    public function getQuery(): Result
    {
        $query[] = "UPDATE $this->tableName SET";

        if ($this->set) {
            $query[] = $this->set;
        }

        if ($this->where) {
            $query[] = "WHERE $this->where";
        }
        if ($this->orderBy) {
            $query[] = "ORDER BY $this->orderBy";
        }
        if ($this->limit) {
            $query[] = "LIMIT $this->limit";
        }
        $query = implode(' ', $query);
        return $this->result->setQuery($query)->setParams($this->params);
    }

}