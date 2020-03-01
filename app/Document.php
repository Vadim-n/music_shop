<?php


namespace App;


use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    public const TYPE_IMAGE = 0;

    protected $table = 'documents';

    /**
     * @var string
     */
    private $tempPath;

    /**
     * @var bool
     */
    private $isMain = false;

    public function products()
    {
        return $this->belongsToMany(Product::class, 'document_product')
            ->withTimestamps()
            ->withPivot('is_main');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return "{$this->name}.{$this->extension}";
    }

    /**
     * @return string
     */
    public function getTempPath(): string
    {
        return $this->tempPath;
    }

    /**
     * @param string $tempPath
     * @return Document
     */
    public function setTempPath(string $tempPath): Document
    {
        $this->tempPath = $tempPath;
        return $this;
    }

    /**
     * @return bool
     */
    public function isMain(): bool
    {
        return $this->isMain;
    }

    /**
     * @param bool $isMain
     * @return Document
     */
    public function setIsMain(bool $isMain): Document
    {
        $this->isMain = $isMain;
        return $this;
    }

    public function getFullPath()
    {
        return $this->collection . DIRECTORY_SEPARATOR . $this->getFullName();
    }
}
