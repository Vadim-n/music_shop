<?php


namespace App\Services;


use App\Category;
use App\Document;
use App\Exceptions\ProductException;
use App\Helpers\AliasTrait;
use App\Product;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpParser\Comment\Doc;
use Webpatser\Uuid\Uuid;

class ProductService
{
    use AliasTrait;

    /**
     * @var DocumentStorage
     */
    private $documentStorage;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(DocumentStorage $documentStorage, ProductRepository $productRepository)
    {
        $this->documentStorage = $documentStorage;
        $this->productRepository = $productRepository;
    }

    /**
     * @param Request $request
     * @param int|null $productId
     * @return Product
     * @throws ProductException
     */
    public function editProduct(array $params, array $files, ?int $productId = null): Product
    {
        $product = $productId ? $product = Product::find($productId) : new Product();

        if (!$product) {
            throw new ProductException("Product $productId has not been found");
        }

        DB::beginTransaction();
        try {
            $alias = $this->checkAliasExistance(
                Product::class,
                $params['alias'] ? $params['alias'] : $this->getAlias($params['name']),
                $productId
            );

            $product->name = $params['name'];

            if ($params['price'] && $params['price'] >= 1) {
                $product->price = $params['price'];
            }

            $product->short_name = isset($params['short_name']) ? $params['short_name'] : null;
            $product->description = $params['description'];
            $product->is_active = $params['is_active'] === 'true' || $params['is_active'] === '1';
            $product->created_by = Auth::user() ? Auth::user()->id : 1;
            $product->alias = $alias;
            $product->meta_title = isset($params['meta_title']) ? $params['meta_title'] : null;
            $product->meta_description = isset($params['meta_description']) ? $params['meta_description'] : null;
            $product->save();

//            Categories
            $productCategories = $this->productRepository->getProductActiveCategories($product->id);

            $updatedCategories = explode(',', $params['categories']);
            foreach ($productCategories as $productCategory) {
                if (!in_array($productCategory->category_id, $updatedCategories)) {
                    $product->categories()->detach($productCategory->category_id);
                    continue;
                }

                $index = array_search($productCategory->category_id, $updatedCategories);

                if ($index !== false) {
                    unset($updatedCategories[$index]);
                }
            }

            foreach ($updatedCategories as $updatedCategory) {
                $category = Category::find($updatedCategory);
                $product->categories()->save($category);
            }

//            Images
            $oldImagesIds = explode(',', $params['old_images']);

            $documentIds = DB::table('document_product')
                ->where('product_id', $product->id)
                ->whereNotIn('document_id', $oldImagesIds)
                ->select('document_id')
                ->get();

            foreach ($documentIds as $item) {
                $document = Document::find($item->document_id);

                Storage::disk(DocumentStorage::LOCAL_DISK)->delete($document->getFullPath());

                $document->delete();
            }

            $oldImages = Document::whereIn('id', $oldImagesIds)->get();

            foreach ($oldImages as $index => $image) {
                DB::table('document_product')
                    ->where('product_id', $product->id)
                    ->where('document_id', $image->id)
                    ->update([
                        'is_main' => $index === (int)$params['main_image_index'],
                        'updated_at' => new \DateTime(),
                    ]);
            }

            foreach ($files as $index => $file) {
                $document = new Document();
                $document->name = Uuid::generate(4);
                $document->extension = $file->getClientOriginalExtension();
                $document->collection = DocumentStorage::COLLECTION_PRODUCTS;
                $document->type = Document::TYPE_IMAGE;
                $document->created_by = Auth::user()->id;

                $path = $file->storeAs(
                    $document->collection,
                    $document->getFullName(),
                    [
                        'disk' => DocumentStorage::LOCAL_DISK,
                    ]
                );

                $document->setTempPath($path);
                $document->setIsMain($index === (int)$params['main_image_index']);

                $product->documents()->save($document, ['is_main' => $document->isMain()]);
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
        DB::commit();

        return $product;
    }

    /**
     * @param Product $product
     */
    public function toggleActivation(Product $product)
    {
        $product->is_active = !$product->is_active;
        $product->save();
    }

    public function order(Request $request, int $categoryId): void
    {
        DB::beginTransaction();
        $category = Category::find($categoryId);

        if (!$category) {
            DB::rollBack();
            throw new ProductException("Category #{$categoryId} has not been found");
        }

        $order = 0;
        foreach ($request->products as $item) {

            $category->products()->updateExistingPivot(
                $item['id'],
                [
                    'order' => $order,
                ]
            );

            $order++;
        }
        DB::commit();
    }
}
