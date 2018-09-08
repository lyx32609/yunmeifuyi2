<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Shop */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shop-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'shop_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shop_type')->textInput() ?>

    <?= $form->field($model, 'shop_source')->textInput() ?>

    <?= $form->field($model, 'shop_status')->textInput() ?>

    <?= $form->field($model, 'shop_priority')->textInput() ?>

    <?= $form->field($model, 'shop_longitude')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shop_latitude')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shop_image')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'company_category_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shop_review')->textInput() ?>

    <?= $form->field($model, 'shop_addr')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'shop_domain')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'createtime')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
