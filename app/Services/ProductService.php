<?php

namespace App\Services;

use App\DTO\ProductFilterDTO;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    public function list(ProductFilterDTO $dto): LengthAwarePaginator
    {
        $query = Product::query();

        if ($dto->filters) {
            $query->filter($dto->filters);
        }

        if ($dto->sort) {
            $query->sort($dto->sort);
        }

        return $query->paginate(15)->appends($dto->queryString);
    }
}
