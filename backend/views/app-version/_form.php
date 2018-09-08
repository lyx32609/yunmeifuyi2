<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\AppVersion */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="app-version-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label('版本号') ?>

    <?= $form->field($model, 'download')->textInput(['maxlength' => true])->label('下载地址') ?>

    <?= $form->field($model, 'addDate')->textInput(['maxlength' => true])->label('添加日期') ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'content')->textarea(['rows' => 6])->label('更新内容') ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
