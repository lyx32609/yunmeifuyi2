<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderDelivery */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-delivery-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'order_id')->textInput(['maxlength' => true])->label("订单id") ?>

    <?= $form->field($model, 'user_id')->textInput(['maxlength' => true])->label("用户id") ?>

    <?= $form->field($model, 'member_id')->textInput(['maxlength' => true])->label("采购商id") ?>

    <?= $form->field($model, 'car_id')->textInput(['maxlength' => true])->label("车辆编号") ?>

    <?= $form->field($model, 'status')->dropDownList(['0'=>'作废','1'=>'扫码装车','2'=>'已发车','3'=>'已签收'])->label("状态") ?>

    <?= $form->field($model, 'scan_time')->textInput(['maxlength' => true])->label("扫码发货时间") ?>

    <?= $form->field($model, 'depart_time')->textInput(['maxlength' => true])->label("发车时间") ?>

    <?= $form->field($model, 'sign_for_time')->textInput(['maxlength' => true])->label("签收时间") ?>

    <?= $form->field($model, 'batch_no')->textInput(['maxlength' => true])->label("车次的编号") ?>

    <?= $form->field($model, 'batch_status')->textInput()->dropDownList(['1'=>'正常','2'=>'完成','0'=>'作废'])->label("车次状态") ?>

    <?= $form->field($model, 'pay_sign_status')->textInput()->label("扫码发货时间") ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
