<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\OfferSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Offers');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="offer-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Offer'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'ownCategory.name',
            'available:boolean',
            'url:url',
            [
                'attribute' => 'pictures',
                'value' => function (\app\models\Offer $model) {
                    $html = '';
                    foreach ($model->smallImages as $imgUrl) {
                        $html .= Html::img($imgUrl, ['class' => 'img-thumbnail']);
                    }
                    return $html;
                },
                'format' => 'raw',
            ],
            'price',
            'currency',
            'name',
            'description:ntext',
            'created_at:dateTime',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
