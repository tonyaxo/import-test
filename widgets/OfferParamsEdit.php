<?php

namespace app\widgets;


use app\models\Offer;
use yii\base\InvalidConfigException;
use yii\bootstrap\Html;
use yii\bootstrap\InputWidget;

class OfferParamsEdit extends InputWidget
{
    /**
     * @var Offer
     */
    public $model;

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!($this->model instanceof Offer)) {
            throw new InvalidConfigException('Property $model must be instance of ' . Offer::class);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $this->registerClientScript();
        foreach ($this->model->params as $index => $param) {
            $nameOptions['value'] = $param['name'];
            $valueOptions['value'] = $param['value'];
            $id = "offer{$this->model->id}_param{$index}";
            echo '<div id="' . $id . '">';
            echo Html::activeInput('text', $this->model, "{$this->attribute}[{$index}][name]", $nameOptions);
            echo Html::activeInput('text', $this->model, "{$this->attribute}[{$index}][value]", $valueOptions);
            echo '</div>';
        }
    }

    /**
     *
     */
    public function registerClientScript(): void
    {
        // TODO Скрипты для работы с атрибутами
    }
}
