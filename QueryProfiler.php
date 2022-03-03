<?php

namespace common\db;

class QueryProfiler
{
    private array $queries = [];

    /**
     * @param string $query
     * @return void
     */
    public function add(string $query)
    {
        $this->queries[] = $query;
    }

    /**
     * @return array
     */
    public function getInfo(): array
    {
        return $this->queries;
    }
}