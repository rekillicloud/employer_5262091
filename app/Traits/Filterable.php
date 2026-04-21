<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        foreach ($filters as $field => $value) {
            if (is_array($value) && isset($value['type'])) {
                $type = $value['type'];
                $column = $this->validateColumn($value['column'] ?? $field);

                match ($type) {
                    'date_range' => $this->applyDateRangeFilter($query, $column, $value['from'] ?? null, $value['to'] ?? null),
                    'between' => $this->applyBetweenFilter($query, $column, $value),
                    'in' => $this->applyInFilter($query, $column, $value['values'] ?? []),
                    'like' => $this->applyLikeFilter($query, $column, $value['value'] ?? ''),
                    'boolean' => $this->applyBooleanFilter($query, $column, $value['value'] ?? $value),
                    default => throw new \InvalidArgumentException("Неизвестный тип фильтрации: {$type}"),
                };
            } elseif (is_array($value)) {
                $column = $this->validateColumn($field);
                $query->where($column, $value);
            } else {
                if (method_exists($this, 'scopeFilterBy' . ucfirst($field))) {
                    $query->{$this->filterMethodName('FilterBy' . ucfirst($field))}($value);
                } else {
                    $column = $this->validateColumn($field);
                    $query->where($column, $value);
                }
            }
        }

        return $query;
    }

    protected function validateColumn(string $column): string
    {
        $allowed = $this->getAllowedFilterColumns();

        if (!in_array($column, $allowed)) {
            throw new \InvalidArgumentException("Фильтр по столбцу '{$column}' не разрешён для модели " . static::class);
        }

        return "{$this->getTable()}.{$column}";
    }

    protected function getAllowedFilterColumns(): array
    {
        return property_exists($this, 'filterableColumns')
            ? $this->filterableColumns
            : [];
    }

    protected function applyDateRangeFilter(Builder $query, string $column, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->whereDate($column, '>=', $from);
        }

        if ($to) {
            $query->whereDate($column, '<=', $to);
        }

        return $query;
    }

    protected function applyBetweenFilter(Builder $query, string $column, array $value): Builder
    {
        if (isset($value['min'])) {
            $query->where($column, '>=', $value['min']);
        }
        if (isset($value['max'])) {
            $query->where($column, '<=', $value['max']);
        }

        return $query;
    }

    protected function applyInFilter(Builder $query, string $column, array $values): Builder
    {
        return $query->whereIn($column, $values);
    }

    protected function applyLikeFilter(Builder $query, string $column, string $value): Builder
    {
        if (!$value) {
            return $query;
        }

        return $query->where($column, 'LIKE', "%{$value}%");
    }

    protected function applyBooleanFilter(Builder $query, string $column, mixed $value): Builder
    {
        return $query->where($column, (bool) $value);
    }

    protected function filterMethodName(string $method): string
    {
        return 'scope' . $method;
    }
}
