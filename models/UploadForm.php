<?php

namespace app\models;

use yii\base\Model;
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
        if ($this->validate()) {
            $this->filename = \Yii::getAlias('@runtime') . '/uploads/' . $this->zipFile->baseName . '.' . $this->zipFile->extension;
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
