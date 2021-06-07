<?php

namespace App\Http\Helper\Debugging;

/**
 * Class GetSqlWithBindings
 * @package Debugging
 *
 */
class GetSqlWithBindings
{
    // Global variables
    public $query;

    public function __construct($query) {
        $this->query = $query;
    }

    public function getQuery() {
        return vsprintf(str_replace('?', '%s', $this->query->toSql()), collect($this->query->getBindings())
            ->map(function ($binding) {
                return is_numeric($binding) ? $binding : "'{$binding}'";
            })->toArray());
    }
}
