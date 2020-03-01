<?php


namespace App\Http\Controllers;


use Illuminate\Http\Request;

class ProductController
{
    public function index(Request $request)
    {
        $successMessage = $request->session()->get('successMessage');
        $request->session()->remove('successMessage');
        return view(
            'product.index',
            compact('successMessage')
        );
    }

    public function add()
    {
        return view('product.add');
    }

    public function edit($productId)
    {
        return view('product.edit', compact('productId'));
    }

    public function order()
    {
        return view('product.order');
    }
}
