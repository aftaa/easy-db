<?php

namespace common\db\DBAL;

class RemoveBuilder
{
    protected string $tableName = '';
    protected string $orderBy = '';
    protected int $limit = 0;
    protected string $where = '';
    protected array $params = [];

    public function __construct(
        protected Result $result,
    )
    {
    }

    /**
     * @param string $tableName
     * @return $this
     */
    public function setTableName(string $tableName): static
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * @param string $where
     * @return $this
     */
    public function where(string $where): static
    {
        $this->where = $where;
        return $this;
    }

    /**
     * @param int $limit
     * @return $this
     */
    public function limit(int $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param string $orderBy
     * @return $this
     */
    public function orderBy(string $orderBy): static
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function setParam($name, $value): static
    {
        $this->params[$name] = $value;
        return $this;
    }

    /**
     * @return Result
     */
    public function getQuery(): Result
    {
        $query[] = "DELETE FROM $this->tableName";
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
        /** @var Result $result */
        $result = $this->result->setQuery($query)->setParams($this->params);
        return $result;
    }
}
