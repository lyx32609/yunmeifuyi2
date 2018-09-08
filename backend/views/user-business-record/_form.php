<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserBusiness */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-business-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'customer_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_tel')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_source')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_state')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_priority')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_longitude')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_latitude')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_photo_str')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_business_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_business_describe')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'staff_num')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'time')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'domain_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'customer_user')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
