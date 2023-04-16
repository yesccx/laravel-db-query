<?php

declare(strict_types = 1);

namespace Yesccx\DBQuery\Contracts;

interface DBQueryContract
{
    /**
     * Connection client
     *
     * @param string|array $connectionName connection name or connection config
     * @param bool $once
     */
    public static function connection(string|array $connectionName, bool $once);

    /**
     * Use SQL statement
     *
     * @param string $statement
     * @param array $bindings
     */
    public function statement(string $statement, array $bindings);
}
