<?php

namespace App\Http\Controllers;

use App\DTO\ProductFilterDTO;
use App\Http\Requests\ProductFilterRequest;
use App\Services\ProductService;

class ProductController extends Controller
{
    public function index(ProductFilterRequest $request, ProductService $service)
    {
        $dto = ProductFilterDTO::fromRequest($request);

        $products = $service->list($dto);

        return view('products.index', compact('products'));
    }
}
