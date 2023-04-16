<?php

declare(strict_types = 1);

namespace Yesccx\DBQuery\Foundation;

use Illuminate\Database\MySqlConnection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Traits\ForwardsCalls;
use Yesccx\DBQuery\Supports\Grammar;

final class QueryWrapper
{
    use ForwardsCalls;

    /**
     * @var Builder
     */
    public Builder $builder;

    /**
     * @param string $statement
     * @param array $bindings
     */
    public function __construct(
        protected string $statement,
        protected array $bindings = []
    ) {
        $this->builder = new Builder(
            new MySqlConnection(fn () => true)
        );
    }

    /**
     * Get SQL
     *
     * @return string
     */
    public function getSql(): string
    {
        return (new Grammar)->resolve($this->builder, $this->statement);
    }

    /**
     * Get bindings
     *
     * @return array
     */
    public function getBindings(): array
    {
        return $this->builder->getBindings();
    }

    /**
     * Proxy Builder
     *
     * @param string $method
     * @param array $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->forwardDecoratedCallTo($this->builder, $method, $parameters);
    }
}
