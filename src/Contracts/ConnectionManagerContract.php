<?php

declare(strict_types = 1);

namespace Yesccx\DBQuery\Contracts;

interface ConnectionManagerContract
{
    /**
     * Make once db client
     *
     * @param string|array $connectionName
     */
    public function once(string|array $connectionName);

    /**
     * Make db client
     *
     * @param string|array $connectionName
     * @param bool $once
     */
    public function get(string|array $connectionName, bool $once = false);
}
