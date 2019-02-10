<?php

namespace app\models;

use app\interfaces\FileImportInterface;
use yii\helpers\FileHelper;
use yii\helpers\Json;

/**
 * Class ImportZip
 * @package app\models
 */
class ImportZip implements FileImportInterface
{
    protected const CATEGORIES_FILENAME = 'category.xml';
    protected const OFFERS_FILENAME_PATTERN = '/offers_(\d+)\.xml/i';

    protected const BATCH_SIZE = 100;

    /**
     * @var string Source ZIP file path.
     */
    protected $zipFilename;
    /**
     * @var string Directory to extract contents of `$zipFilename`.
     */
    protected $dirToExtract;
    /**
     * @var string
     */
    protected $tmpImgDir;
    /**
     * @var array
     */
    protected $results = [
        'total' => 0,
        'categories' => 0,
        'offers' => 0,
    ];

    /**
     * @inheritdoc
     */
    public function execute(): bool
    {
        $transaction = \Yii::$app->getDb()->beginTransaction();
        try {
            $this->prepare();

            $this->processCategories();
            $this->processOffers();
            $this->processImages();

            $transaction->commit();
            return true;
        } catch (\Throwable $t) {
            \Yii::error($t->getMessage() . PHP_EOL . $t->getTraceAsString());

            $this->restoreImages();
            $transaction->rollBack();
            return false;
        } finally {
            $this->clearUp();
        }
    }

    /**
     * @inheritdoc
     */
    public function setFile(string $filename): void
    {
        $this->zipFilename = $filename;
    }

    /**
     * @inheritdoc
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * Update category counter.
     * @param int $count
     */
    public function incCategories(int $count): void
    {
        $this->results['total'] += $count;
        $this->results['categories'] += $count;
    }

    /**
     * Update offers counter.
     * @param int $count
     */
    public function incOffers(int $count): void
    {
        $this->results['total'] += $count;
        $this->results['offers'] += $count;
    }

    /**
     *
     */
    public function prepare(): void
    {
        $this->truncateTables();
        $this->backupImages();
        if ($this->extract() === false) {
            throw new \Error('Zip extraction error');
        }
    }

    /**
     * Remove all data from tables.
     */
    public function truncateTables(): void
    {
        \Yii::$app->getDb()->createCommand()->delete('{{$offer_category}}');
        Offer::deleteAll();
        Category::deleteAll();
    }

    /**
     * @throws \yii\base\ErrorException
     * @throws \yii\base\Exception
     */
    public function backupImages(): void
    {
        $imagesDir = Offer::getImagesDir();
        $this->tmpImgDir = \Yii::getAlias('@runtime') .  DIRECTORY_SEPARATOR . uniqid('tmp_offer_imgs_');
        FileHelper::copyDirectory($imagesDir, $this->tmpImgDir);
        if ($this->imagesBackupExists()) {
            FileHelper::removeDirectory($imagesDir);
            FileHelper::createDirectory($imagesDir);
        } else {
            throw new \Error('Can not create images backup');
        }
    }

    /**
     * Unpack source zip to tmp dir.
     * @return bool
     * @throws \yii\base\Exception
     */
    protected function extract(): bool
    {
        $tmpDir = uniqid('import_');
        $this->dirToExtract = \Yii::getAlias('@runtime') . DIRECTORY_SEPARATOR . $tmpDir;
        if (FileHelper::createDirectory($this->dirToExtract) === false) {
            return false;
        }

        $zip = new \ZipArchive();
        $res = $zip->open($this->zipFilename);
        if ($res === true) {
            $zip->extractTo($this->dirToExtract);
            $zip->close();
            return true;
        } else {
            return false;
        }
    }

    /**
     * Read categories from file and import results to db.
     * @return bool
     * @throws \yii\db\Exception
     */
    protected function processCategories(): bool
    {
        $filename = $this->dirToExtract . DIRECTORY_SEPARATOR . self::CATEGORIES_FILENAME;
        $r = new CategoryReader();
        if ($r->open($filename) === false) {
            return false;
        }
        $batchSize = self::BATCH_SIZE;
        $batch = [];
        foreach ($r as $id => $category) {
            $batch[] = $category;
            $batchSize--;

            if ($batchSize === 0) {
                $this->saveCategories($batch);
                $batchSize = self::BATCH_SIZE;
                $batch = [];
            }
        }
        if ($batch !== []) {
            $this->saveCategories($batch);
        }
        return true;
    }

    /**
     * Insert data to `category` table and update counters.
     * @param array $categories
     * @throws \yii\db\Exception
     */
    protected function saveCategories(array &$categories): void
    {
        $inserted = \Yii::$app->getDb()->createCommand()
            ->batchInsert(Category::tableName(), ['id', 'parent_id', 'name'], $categories)
            ->execute();
        $this->incCategories($inserted);
    }

    /**
     * Read offers from all files and import results to db.
     * @return bool
     * @throws \yii\db\Exception
     */
    protected function processOffers(): bool
    {
        $offersFiles = $this->findOffersFiles();
        foreach ($offersFiles as $filename) {
            $r = new OfferReader();
            if ($r->open($filename) === false) {
                return false;
            }
            $batchSize = self::BATCH_SIZE;
            $batch = $batchCategories = [];
            foreach ($r as $id => $offer) {
                foreach ($offer['categories'] as $cat) {        // collect offers categories
                    $batchCategories[] = ['offer_id' => $id, 'category_id' => $cat];
                }
                unset($offer['categories']);

                $batch[] = $offer;  // collect offers
                $batchSize--;
                if ($batchSize === 0) {     // flush offers and categories
                    $this->saveOffers($batch);
                    $this->saveOfferCategories($batchCategories);
                    $batchSize = self::BATCH_SIZE;
                    $batch = $batchCategories = [];
                }
            }
            if ($batch !== []) {
                $this->saveOffers($batch);
                $this->saveOfferCategories($batchCategories);
            }
        }
        return true;
    }

    /**
     * Insert data to `offer` table.
     * @param array $offers
     * @throws \yii\db\Exception
     */
    protected function saveOffers(array &$offers): void
    {
        $columns = array_keys(current($offers));
        $inserted = \Yii::$app->getDb()->createCommand()
            ->batchInsert(Offer::tableName(), $columns, $offers)
            ->execute();
        $this->incOffers($inserted);
    }

    /**
     * Insert data to `offer_category` table.
     * @param array $links
     * @throws \yii\db\Exception
     */
    protected function saveOfferCategories(array &$links): void
    {
        \Yii::$app->getDb()->createCommand()
            ->batchInsert('{{%offer_category}}', ['offer_id', 'category_id'], $links)
            ->execute();
    }

    /**
     * Store images of offers.
     */
    protected function processImages(): void
    {
        $images = FileHelper::findFiles($this->dirToExtract, [
            'filter' => function ($path) {
                $filename = basename($path);
                if (preg_match('/\.jpg$/i', $filename) === 1) {
                    return true;
                }
                return false;
            }
        ]);
        foreach ($images as $image) {
            $filename = basename($image);
            $dist = Offer::getImagesDir() . DIRECTORY_SEPARATOR . $filename;
            copy($image, $dist);
        }
    }

    /**
     * Returns array of offers files.
     * @return array
     */
    protected function findOffersFiles(): array
    {
        return FileHelper::findFiles($this->dirToExtract, [
            'filter' => function ($path) {
                $filename = basename($path);
                if (preg_match(self::OFFERS_FILENAME_PATTERN, $filename) === 1) {
                    return true;
                }
                return false;
            }
        ]);
    }

    /**
     * @throws \yii\base\ErrorException
     */
    protected function restoreImages(): void
    {
        if ($this->imagesBackupExists()) {
            $imagesDir = Offer::getImagesDir();
            FileHelper::removeDirectory($imagesDir);
            FileHelper::copyDirectory($this->tmpImgDir, $imagesDir);
        }
    }

    /**
     * @return bool
     */
    protected function imagesBackupExists(): bool
    {
        return file_exists($this->tmpImgDir) && is_dir($this->tmpImgDir);
    }

    /**
     * Remove all tmp files.
     * @throws \yii\base\ErrorException
     */
    protected function clearUp(): void
    {
        FileHelper::removeDirectory($this->dirToExtract);
        FileHelper::removeDirectory($this->tmpImgDir);
        FileHelper::unlink($this->zipFilename);
    }
}
