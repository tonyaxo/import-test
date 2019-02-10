<?php

namespace app\widgets;


use app\models\Offer;
use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\widgets\ListView;

class OfferParamsList extends ListView
{
    /**
     * @var Offer
     */
    protected $_model;

    public $itemOptions = ['tag' => 'tr'];
    public $options = ['tag' => 'table', 'class' => 'table table-striped table-bordered detail-view'];
    public $layout = "{items}";

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        if ($this->hasModel() === false) {
            throw new InvalidConfigException('Property $model is required');
        }
        $this->itemView = [$this, 'renderAttribute'];
        $this->initDataProvider();

        parent::init();
    }

    /**
     * @param $model
     * @param $key
     * @param $index
     * @param $widget
     * @return string
     */
    public function renderAttribute($model)
    {
        $name = ArrayHelper::getValue($model, 'name');
        $value = ArrayHelper::getValue($model, 'value');
        return "<th>{$name}</th><td>{$value}</td>";
    }

    /**
     * @param Offer $model
     */
    public function setModel(Offer $model): void
    {
        $this->_model = $model;
    }

    /**
     * @return bool
     */
    public function hasModel(): bool
    {
        return $this->_model instanceof Offer;
    }

    /**
     * Fills dataProvider
     */
    protected function initDataProvider(): void
    {
        $this->dataProvider = new ArrayDataProvider([
            'allModels' => $this->_model->params,
            'pagination' => [
                'pageSize' => 0,
            ],
        ]);
    }
}
