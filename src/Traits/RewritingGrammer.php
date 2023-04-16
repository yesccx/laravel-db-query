<?php

declare(strict_types = 1);

namespace Yesccx\DBQuery\Traits;

use Illuminate\Contracts\Database\Query\Builder;

/**
 * The following are methods related to rewriting Grammar
 *
 * - compileColumns
 * - concatenateWhereClauses
 * - compileOrders
 * - compileLimit
 * - compileOffset
 */
trait RewritingGrammer
{
    /**
     * Compile the "select *" portion of the query.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param array $columns
     *
     * @return null|string
     */
    protected function compileColumns(Builder $query, $columns): ?string
    {
        // If the query is actually performing an aggregating select, we will let that
        // compiler handle the building of the select clauses, as it will need some
        // more syntax that is best handled by that function to keep things neat.
        if (!is_null($query->aggregate)) {
            return null;
        }

        return $this->columnize($columns);
    }

    /**
     * Format the where clause statements into one string.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param array $sql
     *
     * @return string
     */
    protected function concatenateWhereClauses($query, $sql): string
    {
        return $this->removeLeadingBoolean(implode(' ', $sql));
    }

    /**
     * Compile the "order by" portions of the query.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param array $orders
     *
     * @return string
     */
    protected function compileOrders(Builder $query, $orders): string
    {
        if (!empty($orders)) {
            return implode(', ', $this->compileOrdersToArray($query, $orders));
        }

        return '';
    }

    /**
     * Compile the "limit" portions of the query.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param int $limit
     *
     * @return string
     */
    protected function compileLimit(Builder $query, $limit): string
    {
        return (string) $limit;
    }

    /**
     * Compile the "offset" portions of the query.
     *
     * @param \Illuminate\Database\Query\Builder $query
     * @param int $offset
     *
     * @return string
     */
    protected function compileOffset(Builder $query, $offset): string
    {
        return (string) $offset;
    }
}
