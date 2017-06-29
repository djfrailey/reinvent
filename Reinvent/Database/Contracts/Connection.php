<?php

declare(strict_types=1);

namespace Reinvent\Database\Contracts;

use Reinvent\Support\Collection;

interface Connection
{
    public function __construct(string $host, int $port, string $username, string $password, Collection $options = null);
    public function select(string $table = null) : Query;
    public function insert(string $table = null) : Query;
    public function update(string $table = null) : Query;
    public function delete(string $table = null) : Query;
    public function connect();
    public function getConnection();
}