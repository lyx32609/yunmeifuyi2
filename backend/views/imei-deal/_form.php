<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\PutImei */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="put-imei-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput() ?>

    <?= $form->field($model, 'new_imei_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'submit_time')->textInput() ?>

    <?= $form->field($model, 'department_id')->textInput() ?>

    <?= $form->field($model, 'old_imei_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pass_time')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'old_brand')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'old_submit_time')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'new_brand')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'company_categroy_id')->textInput() ?>

    <?= $form->field($model, 'is_read')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
