<?php

/* @var $this yii\web\View */
/* @var $model \app\models\UploadForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('app', 'Import');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

    <?= $form->field($model, 'zipFile')->fileInput() ?>

    <button>Submit</button>

    <?php ActiveForm::end() ?>
</div>
