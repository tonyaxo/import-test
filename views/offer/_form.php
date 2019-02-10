<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Offer */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="offer-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'own_category_id')->dropDownList(\app\models\Category::getList(), ['prompt' => 'Select']) ?>

    <?= $form->field($model, 'available')->checkbox() ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput() ?>

    <div class="pictures">
        <p><label><?= $model->getAttributeLabel('pictures') ?></label></p>
    <?php foreach ($model->smallImages as $imgUrl) { ?>
        <?= Html::img($imgUrl, ['class' => 'img-thumbnail', 'width' => 200]) ?>
    <?php } ?>
    </div>

    <?= $form->field($model, 'currency')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'params')->widget(\app\widgets\OfferParamsEdit::class, [
        // configure additional widget properties here
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
