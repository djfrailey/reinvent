<?php

declare(strict_types=1);

namespace Reinvent\Database\MySQL;

use Reinvent\Database\Contracts\Connection as ConnectionInterface;
use Reinvent\Database\Contracts\Query;

class Connection implements ConnectionInterface
{
    public function select(string $table = null) : Query
    {
        return (new Query($this->connection))->select($table);
    }
    
    public function insert(string $table = null) : Query
    {
        return (new Query($this->connection))->insert($table);
    }

    public function update(string $table = null) : Query
    {
        return (new Query($this->connection))->update($table);
    }

    public function delete(string $table = null) : Query
    {
        return (new Query($this->connection))->delete($table);
    }

    protected function getDSNPrefix() : string
    {
        return 'mysql';
    }
}