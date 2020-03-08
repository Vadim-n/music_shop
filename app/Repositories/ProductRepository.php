<?php


namespace App\Repositories;


use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository
{
    /**
     * @param Request $request
     * @param int $categoryId
     * @return array
     */
    public function getList(Request $request, ?int $categoryId = null): array
    {
        $limit = $request->input('limit', 9999);
        $offset = $request->input('offset', 0);

        $mapping = [
            'id' => 'p.id',
            'name' => 'p.name',
            'short_name' => 'p.short_name',
            'alias' => 'p.alias',
            'creator_name' => 'u.name',
            'is_active' => 'p.is_active',
        ];

        $products = DB::table('products as p')
            ->leftJoin('users as u', 'p.created_by', '=', 'u.id')
            ->when(
                $categoryId,
                function ($q) use ($categoryId) {
                    $q->join('category_product as cp', 'p.id', 'cp.product_id')
                        ->where('cp.category_id', $categoryId);
                }
            )
            ->when(
                $request->has('$filter'),
                $this->applyFilterClosure($request, $mapping)
            );
        $total = $products->count(DB::raw('DISTINCT p.id'));
        $products = $products
            ->select(
                'p.id',
                'p.name',
                'p.short_name',
                'p.alias',
                'u.name as creator_name',
                'p.is_active',
                'p.price'
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
            'products' => $products,
        ];
    }

    public function getOrderList(int $categoryId)
    {
        return DB::table('products as p')
            ->join('category_product as cp', 'p.id', 'cp.product_id')
            ->where('cp.category_id', $categoryId)
            ->where('p.is_active', true)
            ->select(
                'p.id',
                'p.name',
                'cp.order'
            )
            ->orderBy('cp.order')
            ->get()
            ->transform(function ($item) {
                $item->name = "{$item->name} - #{$item->id}";
                return $item;
            });
    }

    public function getProductActiveCategories(int $productId)
    {
        return DB::table('category_product as cp')
            ->join('categories as c', 'cp.category_id', 'c.id')
            ->where('c.is_active', true)
            ->where('cp.product_id', $productId)
            ->select('cp.category_id')
            ->get();
    }

    public function getProductByAlias(string $alias)
    {
        $product = Product::where('alias', $alias)->first();
        $documents = [];

        if ($product) {
            $documents = DB::table('documents as d')
                ->join('document_product as dp', 'd.id', 'dp.document_id')
                ->where('dp.product_id', $product->id)
                ->select(
                    'd.name as document_name',
                    'is_main'
                )
                ->get()
                ->transform(function ($item) {
                    $item->image = route('file_download', ['documentName' => $item->document_name]);
                    unset($item->document_name);

                    return $item;
                });

            if (count($documents) === 0) {
                $document = new \stdClass();
                $document->image = '/images/front/no_image.png';
                $document->is_main = true;

                $documents->add($document);
            }
        }

        return [$product, $documents];
    }
}
