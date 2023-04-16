<?php

declare(strict_types = 1);

namespace Yesccx\DBQuery\Supports;

use Yesccx\DBQuery\DBQuery;

abstract class DBQueryService
{
    /**
     * Define connection config
     *
     * @return string|array
     */
    abstract protected function connection(): string|array;

    /**
     * Use SQL statement
     *
     * @param string $statement
     * @param array $bindings
     *
     * @return DBQuery
     */
    public function statement(string $statement, array $bindings = []): DBQuery
    {
        return DBQuery::connection($this->connection())->statement($statement, $bindings);
    }
}
