<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\WithdSetting */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="withd-setting-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'set_uid')->textInput() ?>

    <?= $form->field($model, 'set_before')->textInput() ?>

    <?= $form->field($model, 'set_after')->textInput() ?>

    <?= $form->field($model, 'set_time')->textInput() ?>

    <?= $form->field($model, 'set_cont')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
