<?php

namespace common\db\ORM;

use app\storages\GuestbookEntryStorage;
use common\Application;
use common\db\DBAL\QueryBuilder;
use common\DependencyInjection;

class Entity
{
    /**
     * @return QueryBuilder
     * @throws \ReflectionException
     */
    public function selectBuilder(): QueryBuilder
    {
        $storage = $this->getStorage();
        return $storage->selectBuilder();
    }

    /**
     * @param string $class
     * @param string $string
     * @return array
     * @throws \ReflectionException
     */
    public function oneToMany(string $refStorage, string $refColumn): array
    {
        return $this->getStorage($refStorage)->select("{$refColumn} = :id", [':id' => $this->id]);
    }

    /**
     * @param string $refColumn
     * @param string $refStorage
     * @return object|null
     * @throws \ReflectionException
     */
    protected function manyToOne(string $refColumn, string $refStorage): ?object
    {
        /** @var Storage $storage */
        $storage = $this->getStorage($refStorage);
        $entity = $storage->selectOne($this->$refColumn)->asEntity();
        return $entity;
    }

    /**
     * @param string $refStorage
     * @return Storage
     * @throws \ReflectionException
     */
    protected function getStorage(string $refStorage): Storage
    {
        return Application::$serviceContainer->init($refStorage);
    }
}
