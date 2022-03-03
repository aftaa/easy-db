<?php

namespace common\db\utils;

class IsItIdField
{
    public function test(\ReflectionProperty $property): bool
    {
        $attributes = $property->getAttributes();
        foreach ($attributes as $attribute) {
            if ('app\entities\common\db\ORM\Id' == $attribute->getName()) {
                return true;
            }
        }
        return false;
    }
}