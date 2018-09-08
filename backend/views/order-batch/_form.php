<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderBatch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-batch-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput(['maxlength' => true])->label("用户id") ?>

    <?= $form->field($model, 'car_id')->textInput(['maxlength' => true])->label("车辆编号") ?>

    <?= $form->field($model, 'car_name')->textInput(['maxlength' => true])->label("车辆名称") ?>

    <?= $form->field($model, 'car_driver_name')->textInput(['maxlength' => true])->label("司机姓名") ?>

    <?= $form->field($model, 'car_driver_phone')->textInput(['maxlength' => true])->label("司机联系电话") ?>

    <?= $form->field($model, 'batch_no')->textInput(['maxlength' => true])->label("自己生成的批次号") ?>

    <?= $form->field($model, 'batch_wms')->textInput(['maxlength' => true])->label("wms 传过来的批次号") ?>

    <?= $form->field($model, 'status')->dropDownList(['1'=>'正常批次','2'=>'结束车次','0'=>'作废批次','3'=>'发车状态'])->label("状态") ?>

    <?= $form->field($model, 'start_time')->textInput(['maxlength' => true])->label("开始时间") ?>

    <?= $form->field($model, 'end_time')->textInput(['maxlength' => true])->label("结束时间") ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
