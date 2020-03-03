<?php


namespace App\Http\Controllers;


class FrontController
{
    public function index() {
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
        return view('front.category', compact('categoryAlias'));
    }

    public function product(string $categoryAlias, string $productAlias)
    {
        return view('front.product', compact('productAlias'));
    }
}
