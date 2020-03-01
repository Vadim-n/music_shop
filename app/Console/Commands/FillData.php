<?php

namespace App\Console\Commands;

use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(CategoryService $categoryService, ProductService $productService)
    {
        $data = Storage::get('initial_data.json');
        $data = json_decode($data);
        $categories = $data->data;

        foreach ($categories as $category) {
            $this->processCategory($category);
        }
    }

    private function processCategory($category)
    {
        if (isset($category->children)) {
            foreach ($category->children as $child) {
                dump("Cat: " . $category->name);
                $this->processCategory($child);
            }
        }

        if (isset($category->products)) {
            foreach ($category->products as $product) {
                dump("Prod: ". $product->name);
            }
        }
    }
}
