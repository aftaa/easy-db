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
        $storage = $this->getStorage($refStorage);
        return $storage->select("{$refColumn} = :id", [':id' => $this->id]);
    }

    /**
     * @param string $refColumn
     * @param string $refStorage
     * @return object
     * @throws \ReflectionException
     */
    protected function manyToOne(string $refColumn, string $refStorage): object
    {
        $storage = $this->getStorage($refStorage);
        $entity = $storage->selectOne($this->$refColumn);
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
