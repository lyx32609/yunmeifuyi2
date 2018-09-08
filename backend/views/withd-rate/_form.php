<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\WithdRate */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="withd-rate-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pound_money')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pound_percent')->textInput() ?>

    <?= $form->field($model, 'is_open')->textInput() ?>

    <?= $form->field($model, 'is_open_which')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
