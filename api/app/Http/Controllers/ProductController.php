<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends ApiController
{
    public function index(Request $request)
    {
        $products = Product::where('active', true)->with('plans')->orderBy('id', 'desc')->get();
        return $request->response_helper->respond($products->toArray()); // should this be a collection?
    }
}
