<?php

declare(strict_types = 1);

namespace Yesccx\DBQuery\Supports;

use Illuminate\Database\Query\Builder;
use Illuminate\Database\Query\Grammars\MySqlGrammar;
use Yesccx\DBQuery\Traits\RewritingGrammer;

final class Grammar extends MySqlGrammar
{
    use RewritingGrammer;

    /**
     * The components that make up a select clause.
     *
     * @var string[]
     */
    protected array $placeholderComponents = [
        'COLUMNS' => [
            'component' => 'columns',
            'prefix'    => '',
            'default'   => '*',
        ],
        'WHERE' => [
            'component' => 'wheres',
            'prefix'    => 'where',
            'default'   => '1',
        ],
        'GROUPBY' => [
            'component' => 'groups',
            'prefix'    => 'group by',
        ],
        'HAVING' => [
            'component' => 'havings',
            'prefix'    => 'having',
        ],
        'ORDERBY' => [
            'component' => 'orders',
            'prefix'    => 'order by',
        ],
        'LIMIT' => [
            'component' => 'limit',
            'prefix'    => 'limit',
        ],
        'OFFSET' => [
            'component' => 'offset',
            'prefix'    => 'offset',
        ],
    ];

    /**
     * Resolving out statement
     *
     * @param Builder $query
     * @param string $statement
     *
     * @return string
     */
    public function resolve(Builder $query, string $statement): string
    {
        foreach ($this->placeholderComponents as $type => $component) {
            $result = $this->compile($query, $type) ?: ($component['default'] ?? null);

            if (empty($result)) {
                $statement = preg_replace("/{$component['prefix']}[\s\S]*?@{$type}@/i", '', $statement);
            } else {
                $statement = str_replace("@{$type}@", $result, $statement);
            }
        }

        return $statement;
    }

    /**
     * Compile
     *
     * @param Builder $query
     * @param string $type
     * @return string
     */
    protected function compile(Builder $query, string $type): string
    {
        if (
            is_null($component = $this->placeholderComponents[$type]['component'] ?? null) ||
            !isset($query->{$component})
        ) {
            return '';
        }

        $method = 'compile' . ucfirst($component);

        return $this->{$method}($query, $query->{$component});
    }
}
