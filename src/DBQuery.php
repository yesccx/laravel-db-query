<?php

declare(strict_types = 1);

namespace Yesccx\DBQuery;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Traits\ForwardsCalls;
use Yesccx\DBQuery\Contracts\ConnectionManagerContract;
use Yesccx\DBQuery\Contracts\DBQueryContract;
use Yesccx\DBQuery\Contracts\QueriesContract;
use Yesccx\DBQuery\Exceptions\DBClientConnectionException;
use Yesccx\DBQuery\Foundation\QueryWrapper;

/**
 * Mysql Query
 *
 * @mixin \Yesccx\DBQuery\Foundation\Queries
 */
class DBQuery implements DBQueryContract
{
    use ForwardsCalls;

    /**
     * Client
     *
     * @var ConnectionInterface
     */
    protected ConnectionInterface $client;

    /**
     * Queries
     *
     * @var QueriesContract
     */
    protected QueriesContract $queries;

    /**
     * @param string|array $connectionName connection name or connection config
     * @param bool $once
     */
    protected function __construct(string|array $connectionName, bool $once = false)
    {
        $this->client = $this->makeClient($connectionName, $once);
    }

    /**
     * Connection client
     *
     * @param string|array $connectionName connection name or connection config
     * @param bool $once
     * @return static
     */
    public static function connection(string|array $connectionName, bool $once = false): static
    {
        return new static($connectionName, $once);
    }

    /**
     * Make client
     *
     * @param string|array $connectionName connection name or connection config
     * @param bool $once
     *
     * @return ConnectionInterface
     *
     * @throws DBClientConnectionException
     */
    protected function makeClient(string|array $connectionName, bool $once = false): ConnectionInterface
    {
        if (empty($connectionName)) {
            throw new DBClientConnectionException('Connection invalid!');
        }

        return app(ConnectionManagerContract::class)->get($connectionName, $once);
    }

    /**
     * Use SQL statement
     *
     * @param string $statement
     * @param array $bindings
     * @return static
     */
    public function statement(string $statement, array $bindings = []): static
    {
        $this->queries = app(QueriesContract::class, [
            'client'    => $this->client,
            'statement' => new QueryWrapper($statement, $bindings),
        ]);

        return $this;
    }

    /**
     * Get Client
     *
     * @return ConnectionInterface
     */
    public function getConnection(): ConnectionInterface
    {
        return $this->client;
    }

    /**
     * Proxy Queries
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        return $this->forwardDecoratedCallTo($this->queries, $method, $parameters);
    }
}
