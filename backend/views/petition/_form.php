<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Petition */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="petition-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>
    <?= $form->field($model, 'title')->textInput(['maxlength' => true])->label('签呈标题') ?>
    <?= $form->field($model, 'content')->textarea(['rows' => 6])->label('签呈内容') ?>

    <?= $form->field($model, 'master_img')->fileInput([
        'name' => Html::getInputName($model, 'master_img').'[]',
        'multiple' => true,
    ])->label('上传图片(最大限制6个)') ?>

    <?= $form->field($model, 'file')->fileInput([
        'name' => Html::getInputName($model, 'file').'[]',
        'multiple' => true,
    ])->label('上传附件(最大限制6个)') ?>



    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '提交' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
