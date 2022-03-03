<?php

namespace common\db;

use app\config\db\connection\Config;

class Connection
{
    public \PDO $PDO;

    /**
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->connect($config);
    }

    /**
     * @return \PDO
     */
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
