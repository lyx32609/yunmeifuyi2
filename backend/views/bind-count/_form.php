<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\BindCount */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bind-count-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'local_count')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'other_count')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'local_department')->textInput() ?>

    <?= $form->field($model, 'other_department')->textInput() ?>

    <?= $form->field($model, 'operation_id')->textInput() ?>

    <?= $form->field($model, 'operation_content')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
