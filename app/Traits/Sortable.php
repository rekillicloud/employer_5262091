<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Sortable
{
    protected static array $allowedSortDirections = ['asc', 'desc'];

    public function scopeSort(Builder $query, array $sort): Builder
    {
        foreach ($sort as $column => $direction) {
            $column = $this->validateSortColumn($column);
            $direction = strtolower($direction);

            if (!in_array($direction, static::$allowedSortDirections)) {
                $direction = 'asc';
            }

            $query->orderBy("{$this->getTable()}.{$column}", $direction);
        }

        return $query;
    }

    protected function validateSortColumn(string $column): string
    {
        $allowed = $this->getAllowedSortColumns();

        if (!in_array($column, $allowed)) {
            throw new \InvalidArgumentException("Сортировка по столбцу '{$column}' не разрешена для модели " . static::class);
        }

        return $column;
    }

    protected function getAllowedSortColumns(): array
    {
        return property_exists($this, 'sortableColumns')
            ? $this->sortableColumns
            : [];
    }
}
