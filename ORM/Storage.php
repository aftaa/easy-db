<?php

namespace common\db\ORM;

use common\Application;
use common\db\DBAL\QueryBuilder;
use common\db\DBAL\QueryResult;
use common\db\DBAL\RemoveBuilder;
use common\db\DBAL\Result;
use common\db\DBAL\UpgradeBuilder;
use common\db\ORM\Storage\InsertService;
use common\db\ORM\Storage\UpdateService;
use common\db\QueryProfiler;
use common\db\utils\StorageNameToEntityName;
use common\db\utils\StorageNameToTableName;
use common\DependencyInjection;

class Storage
{
    public function __construct(
        private StorageNameToTableName  $storageNameToTableName,
        private StorageNameToEntityName $storageNameToEntityName,
        private InsertService           $insertService,
        private UpdateService           $updateService,
        private QueryProfiler           $queryProfiler,
        private QueryResult             $queryResult,
        private Result                  $result,
    )
    {
    }

    /**
     * @return QueryBuilder
     */
    protected function createQueryBuilder(): QueryBuilder
    {
        $classReflection = new \ReflectionClass($this);
        $storageName = $classReflection->getName();
        $tableName = $this->storageNameToTableName->transform($storageName);
        $entityName = $this->storageNameToEntityName->transform($storageName);

        $queryBuilder = new QueryBuilder($this->queryResult, $this->queryProfiler);
        $queryBuilder->setTable($tableName);
        $queryBuilder->setEntity($entityName);

        Application::$serviceContainer->get(DependencyInjection::class)->addLoadedClass(get_class($queryBuilder));

        return $queryBuilder;
    }

    public function createRemoveBuilder(): RemoveBuilder
    {
        $classReflection = new \ReflectionClass($this);
        $storageName = $classReflection->getName();
        $tableName = $this->storageNameToTableName->transform($storageName);
        $removeBuilder = new RemoveBuilder($this->result);
        Application::$serviceContainer->get(DependencyInjection::class)->addLoadedClass(get_class($removeBuilder));

        return $removeBuilder->setTableName($tableName);
    }

    public function createUpgradeBuilder(): RemoveBuilder
    {
        $classReflection = new \ReflectionClass($this);
        $storageName = $classReflection->getName();
        $tableName = $this->storageNameToTableName->transform($storageName);
        $upgradeBuilder = new UpgradeBuilder($this->result);
        Application::$serviceContainer->get(DependencyInjection::class)->addLoadedClass(get_class($upgradeBuilder));

        return $upgradeBuilder->setTableName($tableName);
    }

    /**
     * @param object $object
     * @return int
     * @throws \ReflectionException
     */
    public function store(object $object): int
    {
        if (empty($object->id)) {
            return $this->insertService->insert($object);
        } else {
            $this->updateService->update($object);
            return $object->id;
        }
    }

    /**
     * @return array
     * @throws \ReflectionException
     */
    public function selectAll(): array
    {
        $results = $this->createQueryBuilder()->getQuery()->getResults();
        return $results;
    }

    /**
     * @param int|string $id
     * @return object|null
     */
    public function selectOne(int|string $id): ?object
    {
        return $this->createQueryBuilder()->where('id = :id')->setParam(':id', $id)->getQuery()->getResult();
    }

    /**
     * @throws \ReflectionException
     * @return object[]
     */
    public function select(string $where, array $param): array
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->createQueryBuilder()->where($where);
            foreach ($param as $name => $value) {
                $queryBuilder->setParam($name, $value);
        }
        return $queryBuilder->getQuery()->getResults();
    }

    /**
     * @return QueryBuilder
     * 
     */
    public function selectBuilder(): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder();
        return $queryBuilder;
    }

    /**
     * @return int
     * @throws \ReflectionException
     */
    public function count(): int
    {
        return (int)$this->createQueryBuilder()->select('COUNT(*) AS count')->getQuery()->getResult(false)['count'];
    }

    public function delete(int|string $id): int
    {
        $removeBuilder = $this->createRemoveBuilder();
        return $removeBuilder->where('id = :id')
            ->setParam(':id', $id, \PDO::PARAM_INT)
            ->getQuery()->getResult();
    }
}
