<?php

namespace common\db;

use app\config\db\connection\Config;

class Connection implements ConnectionInterface
{
    public \PDO $PDO;

    public function __construct(Config $config)
    {
        $this->connect($config);
    }

    public function get(): \PDO
    {
        return $this->PDO;
    }

    /**
     * @param Config $config
     * @return void
     */
    public function connect(Config $config): void
    {
        $this->PDO = new \PDO("mysql:host={$config->hostname};dbname={$config->database}", $config->username, $config->password);
    }
}
