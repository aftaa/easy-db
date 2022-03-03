<?php

namespace common\db;

interface ConnectionInterface
{
    public function get(): \PDO;
}