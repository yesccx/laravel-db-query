<?php

declare(strict_types = 1);

namespace Yesccx\DBQuery\Foundation;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Traits\ForwardsCalls;
use Illuminate\Support\Traits\Macroable;
use Yesccx\DBQuery\Contracts\QueriesContract;
use Yesccx\DBQuery\Supports\QueriesOptions;

/**
 * @mixin \Illuminate\Database\Query\Builder
 */
class Queries implements QueriesContract
{
    use ForwardsCalls, Macroable {
        __call as macroCall;
    }

    /**
     * Queries options
     *
     * @var QueriesOptions
     */
    protected QueriesOptions $options;

    /**
     * @param ConnectionInterface $client
     * @param QueryWrapper $statement
     */
    public function __construct(
        protected ConnectionInterface $client,
        protected QueryWrapper $statement,
    ) {
        $this->options = new QueriesOptions;
    }

    /**
     * Set Statement
     *
     * @param QueryWrapper $statement
     *
     * @return static
     */
    public function setStatement(QueryWrapper $statement): static
    {
        $this->statement = $statement;

        return $this;
    }

    /**
     * Get Statement
     *
     * @return QueryWrapper
     */
    public function getStatement(): QueryWrapper
    {
        return $this->statement;
    }

    /**
     * Set client
     *
     * @param ConnectionInterface $client
     *
     * @return static
     */
    public function setClient(ConnectionInterface $client): static
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return ConnectionInterface
     */
    public function getClient(): ConnectionInterface
    {
        return $this->client;
    }

    /**
     * Enable query caching
     *
     * @param int $ttl second
     *
     * @return static
     */
    public function cache(int $ttl = 0): static
    {
        $this->options->setTTL($ttl);

        return $this;
    }

    /**
     * Get caching ttl second
     *
     * @return int
     */
    protected function getCacheTTL(): int
    {
        return $this->options->getTTL();
    }

    /**
     * Execute the query as a "select" statement.
     *
     * @return Collection
     */
    public function get(): Collection
    {
        $ttl = $this->getCacheTTL();

        // Caching conditions
        // TTL > 0 AND db-query.cache.enabled is true
        $cacheEnabled = $ttl > 0 && config('db-query.cache.enabled', false);

        $handler = fn () => $this->client->select(
            $this->statement->getSql(),
            $this->statement->getBindings()
        );

        return $this->formatVia(
            match (true) {
                $cacheEnabled => Cache::store(config('db-query.cache.driver'))->remember(
                    'ydq:' . md5($this->statement->getSql() . json_encode($this->statement->getBindings())),
                    $ttl,
                    $handler
                ),
                default => $handler()
            }
        );
    }

    /**
     * Execute the query and get the first result.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function first(mixed $default = null): mixed
    {
        return $this->get()->take(1)->first(default: $default);
    }

    /**
     * First alias
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function find(mixed $default = null): mixed
    {
        return $this->first() ?? $default;
    }

    /**
     * Get the values of a given key.
     *
     * @param string $column
     *
     * @return Collection
     */
    public function pluck(string $column): Collection
    {
        return $this->get()->pluck($column);
    }

    /**
     * Get a single column's value from the first result of a query.
     *
     * @param string $column
     * @param mixed $default
     * @return mixed
     */
    public function value(string $column, mixed $default = null): mixed
    {
        return $this->first()[$column] ?? $default;
    }

    /**
     * Determine if any rows exist for the current query.
     *
     * @return bool
     */
    public function exists(): bool
    {
        return $this->get()->count() > 0;
    }

    /**
     * Format select result
     *
     * @param mixed $rawResult
     * @return Collection
     */
    protected function formatVia(mixed $rawResult): Collection
    {
        return new Collection(
            json_decode(json_encode($rawResult), true)
        );
    }

    /**
     * Proxy QueryWrapper
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        // TODO: Limit (Query)available methods

        if (static::hasMacro($method)) {
            return $this->macroCall($method, $parameters);
        }

        return $this->forwardDecoratedCallTo($this->statement, $method, $parameters);
    }
}
