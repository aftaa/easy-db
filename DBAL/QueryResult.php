<?php

namespace common\db\DBAL;

use common\db\Connection;
use common\db\ORM\Cell;
use common\db\ORM\DocComment;
use common\db\QueryProfiler;

class QueryResult
{
    private string $query;
    private string $entity;
    private array $params;

    /**
     * @param Connection $connection
     */
    public function __construct(
        private Connection    $connection,
        private QueryProfiler $queryProfiler,
    )
    {
    }

    /**
     * @param array $params
     * @return void
     */
    public function setParams(array $params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @param string $query
     * @return $this
     */
    public function setQuery(string $query): QueryResult
    {
        $this->query = $query;
        return $this;
    }

    /**
     * @param string $entity
     * @return QueryResult
     */
    public function setEntity(string $entity): QueryResult
    {
        $this->entity = $entity;
        return $this;
    }

    /**
     * @param array $datum
     * @return object
     * @throws \ReflectionException
     */
    public function transformToEntity(array $datum): object
    {
        $row = new $this->entity;
        foreach ($datum as $name => $value) {

            $reflectionProperty = new \ReflectionProperty($this->entity, $name);

            if ($reflectionProperty->getType()->getName() == \DateTimeImmutable::class) {
                $row->$name = $value ? new \DateTimeImmutable($value) : null;
                continue;
            }

            if ($reflectionProperty->getType()->getName() == \DateTime::class) {
                $row->$name = $value ? new \DateTime($value) : null;
                continue;
            }

            if (enum_exists($reflectionProperty->getType()->getName())) {
                $typeName = $reflectionProperty->getType()->getName();
                $row->$name = $value ? $typeName::from($value) : null;
                continue;
            }

            $row->$name = $value;
        }
        return $row;
    }

    /**
     * @param bool $transformToEntity
     * @return mixed
     * @throws \ReflectionException
     */
    public function getResult(bool $transformToEntity = true): mixed
    {
        $stmt = $this->connection->get()->prepare($this->query);
        $stmt->execute($this->params);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        if ($transformToEntity) {
            return $this->transformToEntity($data);
        }
        return $data;
    }

    /**
     * @param bool $transformToEntity
     * @return array
     * @throws \ReflectionException
     */
    public function getResults(bool $transformToEntity = true): array
    {
        $stmt = $this->connection->get()->prepare($this->query);
        $stmt->execute($this->params);
        $data = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $table = [];

        if ($transformToEntity) {
            foreach ($data as $datum) {
                $row = $this->transformToEntity($datum);
                $table[] = $row;
            }
        } else {
            $table = $data;
        }
        return $table;
    }
}