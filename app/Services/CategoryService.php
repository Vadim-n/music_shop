<?php


namespace App\Services;


use App\Category;
use App\Document;
use App\Exceptions\CategoryException;
use App\Helpers\AliasTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Webpatser\Uuid\Uuid;

class CategoryService
{
    use AliasTrait;

    /**
     * @param Request $request
     * @param int|null $categoryId
     * @return Category
     * @throws CategoryException
     */
    public function editCategory(array $params, $file, ?int $categoryId = null): Category
    {
        $category = $categoryId ? $category = Category::find($categoryId) : new Category();

        if (!$category) {
            throw new CategoryException("Category $categoryId has not been found");
        }

        DB::beginTransaction();

        try {

            if (!$params['image'] && $params['image_id']) {
                $document = Document::find($params['image_id']);
                Storage::disk(DocumentStorage::LOCAL_DISK)->delete($document->getFullPath());
                $category->image()->dissociate();
                $category->save();
                $document->delete();
            }

            if ($file) {

                if ($category->image) {
                    $document = $category->image;
                    Storage::disk(DocumentStorage::LOCAL_DISK)->delete($document->getFullPath());
                    $category->image()->dissociate();
                    $category->save();
                    $document->delete();
                }

                $document = new Document();
                $document->name = Uuid::generate(4);
                $document->extension = $file->getClientOriginalExtension();
                $document->collection = DocumentStorage::COLLECTION_CATEGORIES;
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

                $document->save();

                $category->image()->associate($document);
            }

            $alias = $this->checkAliasExistance(
                Category::class,
                $params['alias'] ? $params['alias'] : $this->getAlias($params['name']),
                $categoryId
            );

            $category->name = $params['name'];
            $category->short_name = isset($params['short_name']) ? $params['short_name'] : null;
            $category->description = $params['description'];
            $category->is_active = $params['is_active'] === 'true' || $params['is_active'] == 1;
            $category->created_by = Auth::user() ? Auth::user()->id : 1;
            $category->alias = $alias;
            $category->parent_id = isset($params['parent']) ? $params['parent'] : null;
            $category->save();
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }

        DB::commit();

        return $category;
    }

    /**
     * @param Request $request
     * @throws CategoryException
     */
    public function order(Request $request): void
    {
        DB::beginTransaction();
        $order = 0;
        foreach ($request->categories as $item) {
            $category = Category::find($item['id']);
            if (!$category) {
                DB::rollBack();
                throw new CategoryException("Category #{$item['id']} has not been found");
            }
            $category->order = $order;
            $category->save();

            $order++;
        }
        DB::commit();
    }
}
