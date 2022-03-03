<?php

namespace common\db\DBAL;

use common\db\DBAL\Query;
use common\db\QueryProfiler;

class QueryBuilder
{
    private string $table;
    private string $entity;
    private string $select = '*';
    private string $where = '';
    private array $orderBy = [];
    private ?string $limit = null;
    private ?string $offset = null;
    private array $params = [];

    /**
     * @param QueryResult $queryResult
     * @param QueryProfiler $queryProfiler
     */
    public function __construct(
        private QueryResult $queryResult,
        private QueryProfiler $queryProfiler,
    )
    { }

    /**
     * @param string $name
     * @param int|string|float $value
     * @return $this
     */
    public function setParam(string $name, int|string|float $value): static
    {
        $this->params[$name] = $value;
        return $this;
    }

    /**
     * @param string $select
     * @return $this
     */
    public function select(string $select): static
    {
        $this->select = $select;
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
     * @param $column
     * @param $direction
     * @return $this
     */
    public function orderBy(string $column, int $direction): static
    {
        $this->orderBy = [$column, $direction];
        return $this;
    }

    /**
     * @param string $limit
     * @return $this
     */
    public function limit(string $limit): static
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return QueryResult
     */
    public function getQuery(): QueryResult
    {
        $query[] = 'SELECT ' . $this->select . ' FROM ' . $this->table;
        if ($this->where) {
            $query[] = 'WHERE ' . $this->where;
        }
        if ($this->orderBy) {
            $query[] = 'ORDER BY ' . $this->orderBy[0];
            $query[] = match ($this->orderBy[1]) {
                SORT_DESC => ' DESC',
                default => ' ASC',
            };
        }
        if ($this->limit) {
            $query[] = 'LIMIT ' . $this->limit;
        }
        if ($this->offset) {
            $query[] = 'OFFSET ' . $this->offset;
        }
        $query = implode(' ', $query);
        $this->queryProfiler->add($query);
        /** @var QueryResult $result */
        $result = $this->queryResult->setParams($this->params)->setEntity($this->entity)->setQuery($query);
        return $result;
    }

    /**
     * @param string $table
     * @return QueryBuilder
     */
    public function setTable(string $table): QueryBuilder
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @param string $entity
     * @return QueryBuilder
     */
    public function setEntity(string $entity): QueryBuilder
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * @param int|null $offset
     * @return $this
     */
    public function offset(?int $offset): static
    {
        $this->offset = $offset;
        return $this;
    }
}
