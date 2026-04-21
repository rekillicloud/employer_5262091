<?php

namespace App\DTO;

use App\Http\Requests\ProductFilterRequest;

class ProductFilterDTO
{
    public function __construct(
        public readonly array $filters,
        public readonly ?string $sort,
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

        return new self(
            filters: $filters,
            sort: $request->sort,
        );
    }
}
