<?php

namespace App\Http\Controllers;

use App\DTO\ProductFilterDTO;
use App\Http\Requests\ProductFilterRequest;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(ProductFilterRequest $request)
    {
        $dto = ProductFilterDTO::fromRequest($request);

        $products = Product::paginate(15);

        return view('products.index', compact('products'));
    }
}
