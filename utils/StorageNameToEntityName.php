<?php

namespace common\db\utils;

class StorageNameToEntityName
{
    public function transform(string $storageName): string
    {
        $storageName = str_replace('Storage', '', $storageName);
        return str_replace('storages', 'entities', $storageName);
    }
}
