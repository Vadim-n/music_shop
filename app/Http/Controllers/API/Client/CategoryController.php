<?php


namespace App\Http\Controllers\API\Client;


use App\Repositories\CategoryRepository;

class CategoryController
{
    public function index(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->clientList();

        return response()->json(compact('categories'));
    }
}
