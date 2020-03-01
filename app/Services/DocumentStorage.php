<?php


namespace App\Services;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class DocumentStorage
{
    public const LOCAL_DISK = 'local';
    public const TEMP_DISK = 'temp';

    public const COLLECTION_PRODUCTS = 'products';
    public const COLLECTION_CATEGORIES = 'categories';


    public function processDocuments(array $documents, Model $entity)
    {
        foreach ($documents as $document) {
            $path = str_ireplace(self::TEMP_DISK . '/', '', $document->getTempPath());

            Storage::disk(self::LOCAL_DISK)->put(
                $path,
                Storage::disk(self::TEMP_DISK)->get($document->getTempPath())
            );
            Storage::disk(self::TEMP_DISK)->delete($document->getTempPath());

            $entity->documents()->save($document, ['is_main' => $document->isMain()]);
        }
    }
}
