<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends ApiController
{
    public function index(Request $request)
    {
        $products = Product::where('active', true)->with('plans')->orderBy('id', 'desc')->get();
        return ProductResource::collection($products);
    }
}
