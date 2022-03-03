<?php

namespace common\db\ORM\Storage;

use app\entities\GuestbookEntryStatus;
use common\db\Connection;
use common\db\ORM\Table;
use common\db\QueryProfiler;
use common\db\utils\FindIdField;
use common\db\utils\IsItIdField;
use common\db\utils\StorageNameToTableName;

class InsertService
{

    public function __construct(
        private StorageNameToTableName $storageNameToTableName,
        private Connection             $connection,
        private IsItIdField            $isItIdField,
        private FindIdField            $findIdField,
        private QueryProfiler          $profiler,
    )
    {
    }

    public function insert(object $object): int
    {
        $classReflection = new \ReflectionClass($object);
        $tableName = $this->storageNameToTableName->transform($classReflection->getName());
        $id = $this->findIdField->search($object);

        $classReflection = new \ReflectionClass($object);
        $columns = $placeholders = $values = [];

        foreach ($object as $name => $value) {
            $propertyReflection = $classReflection->getProperty($name);
            if ($this->isItIdField->test($propertyReflection)) {
                if (0 == $value) {
                    continue;
                }
            }

            if (empty($value)) {
                continue;
            }

            $columns[] = $name;
            $placeholders[] = ":$name";


            if (\DateTimeImmutable::class == $propertyReflection->getType()) {
                $value = $value->format('Y-m-d h-i-s');
            }
            if (\DateTime::class == $propertyReflection->getType()) {
                $value = $value->format('Y-m-d h-i-s');
            }

            $typeName = $propertyReflection->getType()->getName();
            if (is_object($value) && enum_exists($typeName)) {
                $value = $value->value;
            }

            $values[":$name"] = $value;
        }
        $query[] = "INSERT INTO $tableName (";
        $query[] = join(', ', $columns);
        $query[] = ") VALUES(";
        $query[] = join(', ', $placeholders);
        $query[] = ")";
        $query = join(' ', $query);
        $this->profiler->add($query);
        $stmt = $this->connection->get()->prepare($query)->execute($values);
        return (int)$this->connection->get()->lastInsertId();
    }
}