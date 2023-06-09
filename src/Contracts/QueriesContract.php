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
     * Get Statement
     *
     * @return QueryWrapper
     */
    public function getStatement(): QueryWrapper;

    /**
     * Set Statement
     *
     * @param QueryWrapper $statement
     */
    public function setStatement(QueryWrapper $statement);

    /**
     * Get Client
     *
     * @return ConnectionInterface
     */
    public function getClient(): ConnectionInterface;

    /**
     * Set Client
     *
     * @param ConnectionInterface $client
     */
    public function setClient(ConnectionInterface $client);
}
