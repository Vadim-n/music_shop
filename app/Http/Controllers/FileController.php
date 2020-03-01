<?php


namespace App\Http\Controllers;


use App\Document;

class FileController
{
    public function download($documentName)
    {
        $document = Document::where('name', $documentName)->first();

        if (!$document) {
            return response()->json([
                'status' => 'error',
                'message' => 'Document has not been found',
            ], 404);
        }

        $path = 'app' . DIRECTORY_SEPARATOR . $document->collection . DIRECTORY_SEPARATOR .
            $document->name . '.' . $document->extension;

        return response()->file(storage_path($path));
    }
}
