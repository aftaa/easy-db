<?php

namespace common\db\utils;

use app\config\db\Config;

class StorageNameToTableName
{
    /**
     * @param Config $config
     * @param CamelCaseToSnakeCase $camelCaseToSnakeCase
     */
    public function __construct(
        private Config $config,
        private CamelCaseToSnakeCase $camelCaseToSnakeCase,
    )
    {
    }

    /**
     * @param string $storageName
     * @return string
     */
    public function transform(string $storageName): string
    {
        $tableName = $storageName;
        $tableName = str_replace('\\', '/', $tableName);
        $tableName = str_replace('Storage', '', $tableName);
        $tableName = basename($tableName);
        if (!$this->config->useSnakeCase) {
            $tableName = $this->camelCaseToSnakeCase->transform($tableName);
        }
        return $tableName;
    }
}
