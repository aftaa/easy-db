<?php

namespace common\db\utils;

use common\db\ORM\Id;

class FindIdField
{
    public function search(object $object)
    {
        $return = false;
        $classReflection = new \ReflectionClass($object);
        foreach ($classReflection->getProperties() as $property) {
            if (!$property->getAttributes()) continue;
            foreach ($property->getAttributes() as $attribute) {
                if (Id::class == $attribute->getName()) {
                    $return = $property->getName();
                    break(2);
                }
            }
        }
        return $return;
    }
}