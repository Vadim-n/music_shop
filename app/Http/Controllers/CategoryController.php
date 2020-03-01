<?php


namespace App\Http\Controllers;


use App\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $successMessage = $request->session()->get('successMessage');
        $request->session()->remove('successMessage');
        return view(
            'category.index',
            compact('successMessage')
        );
    }

    public function add()
    {
        return view('category.add');
    }

    public function edit($categoryId)
    {
        return view('category.edit', compact('categoryId'));
    }

    public function order()
    {
        return view('category.order');
    }

    public function products(Request $request, $categoryId)
    {
        $category = Category::find($categoryId);

        if (!$category) {
            abort(404, "Категория не найдена");
        }

        $successMessage = $request->session()->get('successMessage');
        $request->session()->remove('successMessage');
        $dataUrl = route('api_products_category_list', ['categoryId' => $categoryId]);

        return view(
            'category.products',
            compact('dataUrl', 'categoryId', 'successMessage', 'category')
        );
    }

    public function productsOrder(Request $request, $categoryId)
    {
        $category = Category::find($categoryId);

        if (!$category) {
            abort(404, "Категория не найдена");
        }

        $successMessage = $request->session()->get('successMessage');
        $request->session()->remove('successMessage');

        return view('category.products_order', compact('categoryId', 'successMessage', 'category'));
    }
}
