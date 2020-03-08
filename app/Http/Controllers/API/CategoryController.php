<?php


namespace App\Http\Controllers\API;


use App\Category;
use App\Exceptions\CategoryException;
use App\Http\Controllers\Controller;
use App\Repositories\CategoryRepository;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index(Request $request, CategoryRepository $categoryRepository)
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
            $data = $categoryRepository->getList($request);
        } catch (\Exception $exception) {
            Log::error(
                sprintf("Category creating error: %s", $exception->getMessage()),
                $request->all()
            );
            throw $exception;
        }

        return response()->json([
            'categories' => $data['categories'],
            'total' => $data['total']
        ]);
    }

    /**
     * @param Request $request
     * @param CategoryService $categoryService
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function add(Request $request, CategoryService $categoryService)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'parent' => 'nullable|exists:categories,id'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'fields' => $validator->messages()->getMessages()
            ], 422);
        }

        try {
            /** @var Category $category */
            $category = $categoryService->editCategory($request->all(), $request->file('image'));
        } catch (\Exception $exception) {
            Log::error(
                sprintf("Category creating error: %s", $exception->getMessage()),
                $request->all()
            );
            throw $exception;
        }

        $request->session()->put([
            'successMessage' => "Категория {$category->name} - #{$category->id} успешно сохранена",
        ]);

        return response()->json(
            [
                'status' => 'ok',
                'message' => 'Category has been saved successfully',
            ],
            200
        );
    }

    /**
     * @param Request $request
     * @param CategoryService $categoryService
     * @param $categoryId
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function edit(Request $request, CategoryService $categoryService, $categoryId)
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
            $category = $categoryService->editCategory($request->all(), $request->file('image'), $categoryId);
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

        $request->session()->put([
            'successMessage' => "Категория {$category->name} - #{$category->id} успешно изменена",
        ]);

        return response()->json(
            [
                'status' => 'ok',
                'message' => 'Category has been edited successfully',
            ],
            200
        );
    }

    public function get($categoryId)
    {
        $category = Category::find($categoryId);
        $parent = DB::table('categories')
            ->where('is_active', true)
            ->where('id', $category->parent_id)
            ->select(
                'id as value',
                'name as label'
            )
            ->first();

        $image = null;
        if ($category->image) {
            $image = [
                'source' => route('file_download', ['documentName' => $category->image->name]),
                'file' => $category->image->id,
            ];
        }

        return response()->json(compact('category', 'image', 'parent'), 200);
    }

    public function activate($categoryId)
    {
        $category = Category::find($categoryId);

        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => "Category #$categoryId has not been found",
            ], 404);
        }

        $category->is_active = !$category->is_active;
        $category->save();

        return response()->json([
            'status' => 'ok',
            'message' => "Category #$categoryId has been updated successfully",
        ], 200);
    }

    public function orderList(CategoryRepository $categoryRepository)
    {
        $categories = $categoryRepository->getOrderList();
        return response()->json(compact('categories'), 200);
    }

    public function order(Request $request, CategoryService $categoryService)
    {
        $validator = Validator::make($request->all(), [
            'categories' => 'array|required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'fields' => $validator->messages()->getMessages()
            ], 422);
        }

        try {
            $categoryService->order($request);
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

    public function list(CategoryRepository $categoryRepository)
    {
        return response()->json([
            'categories' => $categoryRepository->getTree(),
        ]);
    }
}
