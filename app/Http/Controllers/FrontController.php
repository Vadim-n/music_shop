<?php


namespace App\Http\Controllers;


use App\Category;
use App\Product;
use Symfony\Component\HttpKernel\Exception\HttpException;

class FrontController
{
    public function index()
    {
        return view('front.index');
    }

    public function contacts()
    {
        return view('front.contacts');
    }

    public function about()
    {
        return view('front.about');
    }

    public function category(string $categoryAlias)
    {
        $category = Category::where('alias', $categoryAlias)->first();

        if (!$category) {
            throw new HttpException(404);
        }

        return view('front.category', compact('category'));
    }

    public function product(string $categoryAlias, string $productAlias)
    {
        $product = Product::with([
                'categories' => function ($query) use ($categoryAlias) {
                    $query->where('alias', $categoryAlias);
                }
            ])
            ->where('alias', $productAlias)
            ->first();

        if (!$product) {
            throw new HttpException(404);
        }

        return view('front.product', compact('product'));
    }
}
