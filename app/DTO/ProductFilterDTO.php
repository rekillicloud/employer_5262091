<?php

namespace App\DTO;

use App\Http\Requests\ProductFilterRequest;

class ProductFilterDTO
{
    protected static array $sortMap = [
        'price_asc' => ['price', 'asc'],
        'price_desc' => ['price', 'desc'],
        'rating_desc' => ['rating', 'desc'],
        'newest' => ['created_at', 'desc'],
    ];

    public function __construct(
        public readonly array $filters,
        public readonly array $sort,
    ) {}

    public static function fromRequest(ProductFilterRequest $request): self
    {
        $filters = [];

        if ($request->filled('q')) {
            $filters['q'] = ['type' => 'like', 'value' => $request->q];
        }

        if ($request->filled('price_from') || $request->filled('price_to')) {
            $between = [];
            if ($request->filled('price_from')) {
                $between['min'] = $request->price_from;
            }
            if ($request->filled('price_to')) {
                $between['max'] = $request->price_to;
            }
            $filters['price'] = ['type' => 'between', 'column' => 'price', ...$between];
        }

        if ($request->filled('category_id')) {
            $filters['category_id'] = $request->category_id;
        }

        if ($request->filled('in_stock')) {
            $filters['in_stock'] = ['type' => 'boolean', 'value' => $request->in_stock];
        }

        if ($request->filled('rating_from')) {
            $filters['rating'] = ['type' => 'between', 'column' => 'rating', 'min' => $request->rating_from];
        }

        $sort = [];
        if ($request->filled('sort')) {
            $sorts = is_array($request->sort) ? $request->sort : [$request->sort];
            foreach ($sorts as $sortKey) {
                if (isset(self::$sortMap[$sortKey])) {
                    [$column, $direction] = self::$sortMap[$sortKey];
                    $sort[$column] = $direction;
                }
            }
        }

        return new self(
            filters: $filters,
            sort: $sort,
        );
    }
}
