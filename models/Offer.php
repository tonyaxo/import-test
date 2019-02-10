<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%offer}}".
 *
 * @property int $id
 * @property int $own_category_id
 * @property int $available
 * @property string $url
 * @property array $pictures
 * @property int $price price value as minimal units
 * @property string $currency
 * @property string $name
 * @property string $description
 * @property array $params
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Category $ownCategory
 * @property Category[] $сategories
 * @property array $smallImages
 */
class Offer extends \yii\db\ActiveRecord
{
    public const THUMBNAIL_SIZE = 200;

    protected $images;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%offer}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['own_category_id', 'available', 'price', 'created_at', 'updated_at'], 'integer'],
            [['pictures', 'params'], 'safe'],
            [['name', 'created_at'], 'required'],
            [['description'], 'string'],
            [['url', 'currency', 'name'], 'string', 'max' => 255],
            [['own_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['own_category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'own_category_id' => Yii::t('app', 'Own Category ID'),
            'available' => Yii::t('app', 'Available'),
            'url' => Yii::t('app', 'Url'),
            'pictures' => Yii::t('app', 'Pictures'),
            'price' => Yii::t('app', 'Price'),
            'currency' => Yii::t('app', 'Currency'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'params' => Yii::t('app', 'Params'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     *
     * @return array
     */
    public function getSmallImages(): array
    {
        if ($this->images === null) {
            $this->images = $this->getThumbnails(self::THUMBNAIL_SIZE, self::THUMBNAIL_SIZE);
        }
        return $this->images;
    }

    public function getThumbnails(int $width, $height): array
    {
        $thumbnails = $this->pictures;
        array_walk($thumbnails, function (&$pic, $key, $dir) {
            $srcImage = "$dir/$pic";
            $pic = Yii::$app->thumbnail->url($srcImage, [
                'thumbnail' => [
                    'width' => self::THUMBNAIL_SIZE,
                    'height' => self::THUMBNAIL_SIZE,
                ],
                'placeholder' => [
                    'width' => self::THUMBNAIL_SIZE,
                    'height' => self::THUMBNAIL_SIZE,
                ],
            ]);

        }, self::getImagesDir());
        return $thumbnails;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOwnCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'own_category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getCategories()
    {
        return $this->hasMany(Category::class, ['id' => 'category_id'])
            ->viaTable('{{%offer_category}}', ['offer_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\OfferQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\OfferQuery(get_called_class());
    }

    public static function getImagesDir(): string
    {
        return \Yii::getAlias('@uploads/offer');
    }
}
