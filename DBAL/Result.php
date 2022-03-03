<?php

namespace common\db\DBAL;

use common\db\Connection;
use common\db\QueryProfiler;

class Result
{
    private array $params;
    private string $query;

    public function __construct(
        private Connection $connection,
        private QueryProfiler $queryProfiler,
    )
    {
    }

    /**
     * @param array $params
     * @return Result
     */
    public function setParams(array $params): Result
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @param string $query
     * @return Result
     */
    public function setQuery(string $query): Result
    {
        $this->query = $query;
        return $this;
    }

    /**
     * @return bool
     */
    public function getResult(): bool
    {
        $this->queryProfiler->add($this->query);
        try {
            return $this->connection->get()->prepare($this->query)->execute($this->params);
        } catch (\PDOException $e) {
            echo "QUERY: $this->query<br>", $e->getMessage();
        }
    }
}
