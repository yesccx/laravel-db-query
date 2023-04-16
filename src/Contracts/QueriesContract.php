<?php

declare(strict_types = 1);

namespace Yesccx\DBQuery\Contracts;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;
use Yesccx\DBQuery\Foundation\QueryWrapper;

interface QueriesContract
{
    /**
     * Execute the query as a "select" statement.
     *
     * @return Collection
     */
    public function get(): Collection;

    /**
     * Set Statement
     *
     * @param QueryWrapper $statement
     */
    public function setStatement(QueryWrapper $statement);

    /**
     * Set Client
     *
     * @param ConnectionInterface $client
     */
    public function setClient(ConnectionInterface $client);
}
