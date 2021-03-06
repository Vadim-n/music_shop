<?php


namespace App\Repositories;


use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryRepository extends BaseRepository
{
    /**
     * @param Request $request
     * @return array
     */
    public function getList(Request $request): array
    {
        $limit = $request->input('limit', 9999);
        $offset = $request->input('offset', 0);

        $mapping = [
            'id' => 'c.id',
            'name' => 'c.name',
            'short_name' => 'c.short_name',
            'alias' => 'c.alias',
            'creator_name' => 'u.name',
            'is_active' => 'c.is_active',
            'parent_name' => 'p.name'
        ];

        $categories = DB::table('categories as c')
            ->leftJoin('users as u', 'c.created_by', '=', 'u.id')
            ->leftJoin('categories as p', 'c.parent_id', 'p.id')
            ->when(
                $request->has('$filter'),
                $this->applyFilterClosure($request, $mapping)
            );
        $total = $categories->count(DB::raw('DISTINCT c.id'));
        $categories = $categories
            ->select(
                'c.id',
                'c.name',
                'c.short_name',
                'c.alias',
                'u.name as creator_name',
                'c.is_active',
                'p.name as parent_name'
            )
            ->when(
                $request->has('$orderBy'),
                $this->applyOrderByClosure($request, $mapping)
            )
            ->skip($offset)
            ->take($limit)
            ->get();

        return [
            'total' => $total,
            'categories' => $categories,
        ];
    }

    public function getOrderList()
    {
        return DB::table('categories')
            ->where('is_active', true)
            ->select(
                'id',
                'name',
                'order'
            )
            ->orderBy('order')
            ->get()
            ->transform(function($item) {
                $item->name = "{$item->name} - #{$item->id}";
                return $item;
            });
    }

    public function getTree()
    {
        return $this->getChildren();
    }

    private function getChildren(?\stdClass $category = null)
    {
        $categoryId = $category ? $category->value : null;

        $categories = DB::table('categories')
            ->where('is_active', true)
            ->where('parent_id', $categoryId)
            ->orderBy('order')
            ->select(
                'id as value',
                'name'
            )
            ->get()
            ->transform(function ($item) {
                $item->label = "{$item->name} - #{$item->value}";
                $item->children = [];

                unset($item->name);

                return $item;
            });

        foreach ($categories as $c) {
            $c->children = $this->getChildren($c);
        }

        return $categories;
    }

    public function clientList()
    {
        return DB::table('categories as c')
            ->leftJoin('documents as d', 'c.image_id', 'd.id')
            ->where('c.is_active', true)
            ->whereNull('c.parent_id')
            ->select(
                'c.alias',
                'c.name',
                'd.name as document_name'
            )
            ->orderBy('c.order')
            ->get()
            ->transform(function ($item) {
                if ($item->document_name) {
                    $item->image = route('file_download', ['documentName' => $item->document_name]);
                } else {
                    $item->image = '/images/front/no_image.png';
                }

                unset($item->document_name);

                return $item;
            });

    }

    /**
     * @param string $alias
     * @return array
     */
    public function getCategoryByAlias(string $alias): array
    {
        $category = Category::where('alias', $alias)->first();
        $children = [];
        $products = [];

        if ($category) {
            $children = DB::table('categories as c')
                ->leftJoin('documents as d', 'c.image_id', 'd.id')
                ->where('c.parent_id', $category->id)
                ->select(
                    'c.name',
                    'c.alias',
                    'd.name as document_name'
                )
                ->orderBy('c.order')
                ->get()
                ->transform(function ($item) {
                    if ($item->document_name) {
                        $item->image = route('file_download', ['documentName' => $item->document_name]);
                    } else {
                        $item->image = '/images/front/no_image.png';
                    }

                    unset($item->document_name);

                    return $item;
                });

            $mainDocuments = DB::table('document_product')
                ->where('is_main', true);

            $products = DB::table('products as p')
                ->join('category_product as cp', 'p.id', 'cp.product_id')
                ->leftJoinSub($mainDocuments, 'dp', 'p.id', 'dp.product_id')
                ->leftJoin('documents as d', 'dp.document_id', 'd.id')
                ->where('cp.category_id', $category->id)
                ->where('p.is_active', true)
                ->select(
                    'p.name',
                    'p.price',
                    'p.alias',
                    'd.name as document_name'
                )
                ->orderBy('p.order')
                ->get()
                ->transform(function ($item) {
                    if ($item->document_name) {
                        $item->image = route('file_download', ['documentName' => $item->document_name]);
                    } else {
                        $item->image = '/images/front/no_image.png';
                    }

                    unset($item->document_name);

                    return $item;
                });
        }

        return [$category, $children, $products];
    }
}
