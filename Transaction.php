<?php

namespace common\db;

use app\config\db\transaction\Config;

class Transaction
{
    public function __construct(
        private Connection $connection,
        private Config     $config,
    )
    {
        $this->setAutocommit($this->config->autoCommit);
    }

    /**
     * @return bool
     */
    public function begin(): bool
    {
        return $this->connection->get()->beginTransaction();
    }

    /**
     * @return bool
     */
    public function commit(): bool
    {
        return $this->connection->get()->commit();
    }

    /**
     * @return bool
     */
    public function rollback(): bool
    {
        return $this->connection->get()->rollback();
    }

    /**
     * @param bool $autocommit
     * @return false|int
     */
    public function setAutocommit(bool $autocommit): bool|int
    {
        $autocommit = (int)$autocommit;
        return $this->connection->get()->exec("SET autocommit=$autocommit");
    }

    /**
     * @return bool
     */
    public function inTransaction(): bool
    {
        return $this->connection->get()->inTransaction();
    }
}
