<?php

declare(strict_type=1);

namespace Reinvent\Database\PDO;

use Reinvent\Database\Contracts\Connection as ConnectionInterface;
use Reinvent\Database\Contracts\Query;

abstract class Connection implements ConnectionInterface
{
    protected $host;
    protected $port;
    protected $username;
    protected $password;
    protected $options;
    protected $database;
    protected $charset;
    protected $connection;

    public function __construct(
        string $host,
        int $port,
        string $username,
        string $password,
        string $database,
        string $charset = 'utf8',
        Collection $options = null)
    {
        $this->host = $host;
        $this->port = $port;
        $this->username = $username;
        $this->password = $password;
        $this->charset = $charset;
        $this->options = $options;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function connect()
    {
        $options = $this->options ? $this->options->toArray() : null;
        $this->connection = new PDO($this->getDSN(), $this->username, $this->password, $options);
    }

    private function getDSN() : string
    {
        $prefix = $this->getDSNPrefix();
        $prefix = rtrim($prefix, ':');
        
        return "$prefix:host={$this->host};port={$this->port};dbname={$this->dbname};charset={$this->charset}";
    }

    abstract public function select(string $table = null) : Query;
    abstract public function insert(string $table = null) : Query;
    abstract public function update(string $table = null) : Query;
    abstract public function delete(string $table = null) : Query;

    abstract protected function getDSNPrefix() : string;
}