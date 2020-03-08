<?php


namespace App\Http\Controllers\API\Client;

use App\Repositories\ProductRepository;

class ProductController
{
    public function get(string $productAlias, ProductRepository $productRepository)
    {
        list($product, $documents) = $productRepository->getProductByAlias($productAlias);

        return response()->json(compact('product', 'documents'));
    }
}
