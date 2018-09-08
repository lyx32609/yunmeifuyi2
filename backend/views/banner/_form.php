<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Banner */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="banner-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'start_time')->widget(\yii\jui\DatePicker::classname(), [
        'language' => 'zh-CN',
        'dateFormat' => 'yyyy-MM-dd',
        'options'=>[
            'class' => 'form-control col-lg-3',
        ],
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
            'todayBtn' => true,
        ]
    ])->label('开始时间');?>

    <?= $form->field($model, 'end_time')->widget(\yii\jui\DatePicker::classname(), [
        'language' => 'zh-CN',
        'dateFormat' => 'yyyy-MM-dd',
        'options'=>[
            'class' => 'form-control col-lg-3',
        ],
        'clientOptions' => [
            'autoclose' => true,
            'format' => 'yyyy-mm-dd',
            'todayBtn' => true,
        ]
    ])->label('结束时间');?>

    <?= $form->field($model, 'images')->widget('common\widgets\file_upload\FileUpload',[
        'config'=>[
        ]
    ]) ?>

    <?= $form->field($model, 'is_valid')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '上传' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
