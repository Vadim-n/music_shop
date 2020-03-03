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

    public function get(string $categoryAlias, CategoryRepository $categoryRepository)
    {
        list($category, $children, $products) = $categoryRepository->getCategoryByAlias($categoryAlias);

        $category->children = $children;
        $category->products = $products;

        return response()->json(compact('category'));
    }
}
