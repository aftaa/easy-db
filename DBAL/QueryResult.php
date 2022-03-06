<?php

namespace common\db\DBAL;

use common\db\Connection;
use DateTime;
use DateTimeImmutable;
use Exception;
use PDO;
use ReflectionException;
use ReflectionProperty;

class QueryResult
{
    private string $query;
    private string $entity;
    private array $params;
    private array $result;

    /**
     * @param Connection $connection
     */
    public function __construct(
        private Connection $connection,
    )
    {
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setParams(array $params): self
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
     * @throws ReflectionException
     */
    public function transformToEntity(array $datum): object
    {
        $row = new $this->entity;
        foreach ($datum as $name => $value) {

            $reflectionProperty = new ReflectionProperty($this->entity, $name);

            if ($reflectionProperty->getType()->getName() == DateTimeImmutable::class) {
                try {
                    $row->$name = $value ? new DateTimeImmutable($value) : null;
                } catch (Exception) {
                }
                continue;
            }

            if ($reflectionProperty->getType()->getName() == DateTime::class) {
                try {
                    $row->$name = $value ? new DateTime($value) : null;
                } catch (Exception) {
                }
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
     * @return $this|null
     */
    public function getResult(): ?self
    {
        $stmt = $this->connection->get()->prepare($this->query);
        $stmt->execute($this->params);
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        $this->result = $data;
        return $this;
    }

    /**
     * @return $this
     */
    public function getResults(): self
    {
        $stmt = $this->connection->get()->prepare($this->query);
        $stmt->execute($this->params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->result = $data;
        return $this;
    }

    /**
     * @throws ReflectionException
     */
    public function asEntity(): object
    {
        return $this->transformToEntity($this->result);
    }

    /**
     * @return array
     */
    public function asArray(): array
    {
        return $this->result;
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public function asEntities(): array
    {
        $table = [];
        foreach ($this->result as $row) {
            $table[] = $this->transformToEntity($row);
        }
        return $table;
    }
}