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
                $column = $value['column'] ?? $field;

                match ($type) {
                    'date_range' => $this->applyDateRangeFilter($query, $column, $value['from'] ?? null, $value['to'] ?? null),
                    'between' => $this->applyBetweenFilter($query, $column, $value),
                    'in' => $this->applyInFilter($query, $column, $value['values'] ?? []),
                    'like' => $this->applyLikeFilter($query, $column, $value['value'] ?? ''),
                    default => $query->where($field, $value['value'] ?? $value),
                };
            } elseif (is_array($value)) {
                $query->where($field, $value);
            } else {
                if (method_exists($this, 'scopeFilterBy' . ucfirst($field))) {
                    $query->{$this->filterMethodName('FilterBy' . ucfirst($field))}($value);
                } else {
                    $query->where($field, $value);
                }
            }
        }

        return $query;
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

    protected function filterMethodName(string $method): string
    {
        return 'scope' . $method;
    }
}
