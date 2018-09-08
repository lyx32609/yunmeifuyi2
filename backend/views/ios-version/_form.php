<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\IosVersion */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ios-version-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'iosDownload')->textInput(['maxlength' => true])->label('更新地址') ?>

    <?= $form->field($model, 'iosForce')->textInput(['maxlength' => true])->label('更新类型')  ?>

    <?= $form->field($model, 'iosUpdateMsg')->textInput(['maxlength' => true])->label('更新内容') ?>

    <?= $form->field($model, 'iosVersion')->textInput(['maxlength' => true])->label('版本号') ?>

    <?= $form->field($model, 'type')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
