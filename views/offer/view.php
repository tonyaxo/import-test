<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Offer */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Offers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="offer-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'own_category_id',
            'available',
            'url:url',
            'price',
            'currency',
            'name',
            'description:ntext',
        ],
    ]) ?>

    <div class="pictures">
        <p><label><?= $model->getAttributeLabel('pictures') ?></label></p>
        <?php foreach ($model->smallImages as $imgUrl) { ?>
            <?= Html::img($imgUrl, ['class' => 'img-thumbnail', 'width' => 200]) ?>
        <?php } ?>
    </div>

    <p><label><?= $model->getAttributeLabel('params') ?></label></p>
    <?= \app\widgets\OfferParamsList::widget([
        'model' => $model,
    ]) ?>


</div>
