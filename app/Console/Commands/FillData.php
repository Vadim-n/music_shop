<?php

namespace App\Console\Commands;

use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FillData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fill:data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill initial data';

    /**
     * @var CategoryService
     */
    private $categoryService;

    /**
     * @var ProductService
     */
    private $productService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CategoryService $categoryService, ProductService $productService)
    {
        parent::__construct();

        $this->categoryService = $categoryService;
        $this->productService = $productService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $data = Storage::get('initial_data.json');
        $data = json_decode($data);
        $categories = $data->data;

        DB::beginTransaction();

        try {
            foreach ($categories as $category) {
//                $categoryNew = $this->categoryService->editCategory(
//                    [
//                        'name' => $category->name,
//                        'image' => null,
//                        'image_id' => null,
//                        'alias' => null,
//                        'description' => null,
//                        'is_active' => 1
//                    ],
//                    null
//                );
//                $category->id = $categoryNew->id;
                $this->processCategory($category);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    private function processCategory($category, $parentId = null)
    {
        $categoryNew = $this->categoryService->editCategory(
            [
                'name' => $category->name,
                'parent' => $parentId,
                'image' => null,
                'image_id' => null,
                'alias' => null,
                'description' => null,
                'is_active' => 1
            ],
            null
        );

        if (isset($category->children)) {
            foreach ($category->children as $child) {
                $this->processCategory($child, $categoryNew->id);
            }
        }

        if (isset($category->products)) {
            foreach ($category->products as $product) {
                $this->productService->editProduct(
                    [
                        'name' => $product->name,
                        'is_active' => true,
                        'description' => null,
                        'price' => null,
                        'old_images' => '',
                        'categories' => (string)$categoryNew->id,
                        'alias' => null,
                    ],
                    []
                );
            }
        }
    }
}
