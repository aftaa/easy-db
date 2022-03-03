<?php

namespace common\db\ORM\Storage;

use common\db\Connection;
use common\db\utils\IsItIdField;
use common\db\utils\StorageNameToTableName;

class UpdateService
{
    public function __construct(
        private Connection             $connection,
        private IsItIdField            $isItIdField,
        private StorageNameToTableName $storageNameToTableName
    )
    {
    }

    /**
     * @param object $object
     * @return int
     * @throws \ReflectionException
     */
    public function update(object $object): int
    {
        $classReflection = new \ReflectionClass($object);
        $tableName = $this->storageNameToTableName->transform($classReflection->getName());
        $placeholders = $values = [];

        foreach ($object as $name => $value) {
            $propertyReflection = $classReflection->getProperty($name);
            if ($this->isItIdField->test($propertyReflection)) {
                continue;
            }

            if (\DateTimeImmutable::class == $propertyReflection->getType()) {
                $value = $value->format('Y-m-d h:i:s');
            }

            if (\DateTime::class == $propertyReflection->getType()) {
                $value = $value->format('Y-m-d h-i-s');
            }

            $typeName = $propertyReflection->getType()->getName();
            if (is_object($value) && enum_exists($typeName)) {
                $value = $value->value;
            }

            $values[":$name"] = $value;
            $placeholders[] = "$name = :$name";
        }
        $values[':id'] = $object->id;
        $query[] = "UPDATE $tableName SET ";
        $query[] = join(', ', $placeholders);
        $query[] = "WHERE id=:id";
        $query = join(' ', $query);
        $this->connection->get()->prepare($query)->execute($values);
        return (int)$object->id;
    }
}