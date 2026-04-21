<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Sortable
{
    public function scopeSort(Builder $query, array $sort): Builder
    {
        foreach ($sort as $column => $direction) {
            $direction = in_array(strtolower($direction), ['asc', 'desc'])
                ? strtolower($direction)
                : 'asc';

            $query->orderBy($column, $direction);
        }

        return $query;
    }
}
