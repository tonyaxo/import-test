<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%category}}".
 *
 * @property int $id
 * @property int $parent_id
 * @property string $name
 *
 * @property Category $parent
 * @property Category[] $categories
 * @property Offer[] $offers
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%category}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['parent_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['parent_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'name' => Yii::t('app', 'Name'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::class, ['id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::class, ['parent_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getOffers()
    {
        return $this->hasMany(Offer::class, ['id' => 'offer_id'])
            ->viaTable('{{%offer_category}}', ['category_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return \app\models\query\CategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\CategoryQuery(get_called_class());
    }

    public static function getList(): array
    {
        return self::find()->select(['name', 'id'])->indexBy('id')->column();
    }
}
