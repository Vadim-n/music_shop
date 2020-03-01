<?php


namespace App\Http\Controllers\API;


use App\Category;
use App\Exceptions\CategoryException;
use App\Exceptions\ProductException;
use App\Helpers\AliasTrait;
use App\Http\Controllers\Controller;
use App\Product;
use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request, ProductRepository $productRepository, $categoryId = null)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'numeric|min:0',
            'offset' => 'numeric|min:0'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'fields' => $validator->messages()->getMessages()
            ], 422);
        }

        try {
            $data = $productRepository->getList($request, $categoryId);
        } catch (\Exception $exception) {
            Log::error(
                sprintf("Product creating error: %s", $exception->getMessage()),
                $request->all()
            );
            throw $exception;
        }

        return response()->json([
            'products' => $data['products'],
            'total' => $data['total']
        ]);
    }

    /**
     * @param Request $request
     * @param ProductService $productService
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function add(Request $request, ProductService $productService)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'fields' => $validator->messages()->getMessages()
            ], 422);
        }

        try {
            /** @var Product $product */
            $product = $productService->editProduct($request);
        } catch (\Exception $exception) {
            Log::error(
                sprintf("Product creating error: %s", $exception->getMessage()),
                $request->all()
            );
            throw $exception;
        }

        $request->session()->put([
            'successMessage' => "Товар {$product->name} - #{$product->id} успешно сохранена",
        ]);

        return response()->json(
            [
                'status' => 'ok',
                'message' => 'Product has been saved successfully',
            ],
            200
        );
    }

    /**
     * @param Request $request
     * @param CategoryService $productService
     * @param $productId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function edit(Request $request, ProductService $productService, $productId)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'fields' => $validator->messages()->getMessages()
            ], 422);
        }

        try {
            $product = $productService->editProduct($request, $productId);
        } catch (ProductException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 404);
        } catch (\Exception $exception) {
            Log::error(
                sprintf("Product editing error: %s", $exception->getMessage()),
                $request->all()
            );
            throw $exception;
        }

        $request->session()->put([
            'successMessage' => "Товар {$product->name} - #{$product->id} успешно изменен",
        ]);

        return response()->json(
            [
                'status' => 'ok',
                'message' => 'Product has been edited successfully',
            ],
            200
        );
    }

    public function get($productId)
    {
        $product = Product::with('documents')
            ->with('categories')
            ->find($productId);


        $files = [];

        foreach ($product->documents as $document) {
            $files[] = [
                'source' => route('file_download', ['documentName' => $document->name]),
                'document_id' => $document->id,
                'is_main' => $document->pivot->is_main,
            ];
        }

        return response()->json(compact('product', 'files'), 200);
    }

    public function activate(ProductService $productService, $productId)
    {
        $product = Product::find($productId);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => "Product #$productId has not been found",
            ], 404);
        }

        $productService->toggleActivation($product);

        return response()->json([
            'status' => 'ok',
            'message' => "Category #$productId has been updated successfully",
        ], 200);
    }

    public function orderList(ProductRepository $productRepository, $categoryId)
    {
        $products = $productRepository->getOrderList($categoryId);
        return response()->json(compact('products'), 200);
    }

    public function order(Request $request, ProductService $productService, $categoryId)
    {
        $validator = Validator::make($request->all(), [
            'products' => 'array|required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'fields' => $validator->messages()->getMessages()
            ], 422);
        }

        try {
            $productService->order($request, $categoryId);
        } catch (CategoryException $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 404);
        } catch (\Exception $exception) {
            Log::error(
                sprintf("Category editing error: %s", $exception->getMessage()),
                $request->all()
            );
            throw $exception;
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'Categories have been ordered successfully',
        ], 200);
    }
}
