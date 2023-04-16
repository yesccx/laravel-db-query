<?php

declare(strict_types = 1);

namespace Yesccx\DBQuery\Supports;

final class QueriesOptions
{
    /**
     * @param int $ttl Cache ttl
     */
    public function __construct(
        protected int $ttl = 0,
    ) {
    }

    /**
     * Get TTL
     *
     * @return int
     */
    public function getTTL(): int
    {
        return $this->ttl;
    }

    /**
     * Set TTL
     *
     * @param int $value
     *
     * @return void
     */
    public function setTTL(int $value): void
    {
        $this->ttl = $value;
    }
}
