<?php

namespace app\models;

use yii\base\Model;
use yii\helpers\FileHelper;
use yii\web\UploadedFile;

/**
 * Class UploadForm
 * @package app\models
 */
class UploadForm extends Model
{
    protected $filename;

    /**
     * @var UploadedFile
     */
    public $zipFile;

    public function rules()
    {
        return [
            [['zipFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'zip'],
        ];
    }

    public function upload(): bool
    {
        $uploadDir = \Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . 'uploads';
        if ($this->validate() && FileHelper::createDirectory($uploadDir)) {

            $this->filename = $uploadDir . DIRECTORY_SEPARATOR . $this->zipFile->baseName . '.' . $this->zipFile->extension;
            $this->zipFile->saveAs($this->filename);
            return true;
        } else {
            return false;
        }
    }

    public function getFilename(): string
    {
        return $this->filename;
    }
}
