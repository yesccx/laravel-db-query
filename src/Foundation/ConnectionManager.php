<?php

declare(strict_types = 1);

namespace Yesccx\DBQuery\Foundation;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use Yesccx\DBQuery\Contracts\ConnectionManagerContract;

/**
 * Connection Manager
 */
class ConnectionManager implements ConnectionManagerContract
{
    /**
     * Connection pools
     *
     * @var array
     */
    protected static array $pools = [];

    /**
     * Make once db client
     *
     * @param string|array $connectionName
     *
     * @return \Illuminate\Database\ConnectionInterface
     */
    public function once(string|array $connectionName): ConnectionInterface
    {
        return $this->get($connectionName, true);
    }

    /**
     * Make db client
     *
     * @param string|array $connectionName
     * @param bool $once
     *
     * @return \Illuminate\Database\ConnectionInterface
     */
    public function get(string|array $connectionName, bool $once = false): ConnectionInterface
    {
        $connectionName = match (true) {
            is_array($connectionName) => $this->defineConnectionConfig(config: $connectionName),
            default                   => $connectionName
        };

        return match (true) {
            $once                                => DB::connection($connectionName),
            empty(self::$pools[$connectionName]) => self::$pools[$connectionName] = DB::connection($connectionName),
            default                              => self::$pools[$connectionName]
        };
    }

    /**
     * Define connection config
     *
     * @param array $config
     * @return string connect name
     */
    protected function defineConnectionConfig(array $config): string
    {
        return tap(
            md5('custom_' . json_encode($config)),
            fn ($name) => config([
                "database.connections.{$name}" => $config,
            ])
        );
    }
}
