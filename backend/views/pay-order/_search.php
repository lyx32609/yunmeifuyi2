<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\OrdersSearch */
/* @var $form yii\widgets\ActiveForm */
/* 获取当前登录人的id */
$uid = \Yii::$app->user->id;
?>


<div class="orders-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class' => 'form-horizontal','autocomplete'=>"off"],
        'fieldConfig' => [
            'template' => "
            <div class='col-xs-2 col-sm-3 text-right'>{label}</div>
            <div class='col-xs-8 col-sm-7'>{input}</div>
            <div class='col-xs-11 col-xs-offset-3 col-sm-2 col-sm-offset-0'>{error}</div>",
        ]
    ]); ?>
    <div class="row">
        <div class="col-xs-6">
            <?php
            echo $form->field($model, 'start_time')->widget(\yii\jui\DatePicker::classname(), [
                'language' => 'zh-CN',
                'dateFormat' => 'yyyy-MM-dd',
                'options' => [
                    'class' => 'form-control col-lg-3', 'placeholder' => "默认一周前",
                ],
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'todayBtn' => true,
                ]
            ])->label('完成开始时间');
            ?>
        </div>
        <div class="col-xs-6">
            <?php
            echo $form->field($model, 'end_time')->widget(\yii\jui\DatePicker::classname(), [
                'language' => 'zh-CN',
                'dateFormat' => 'yyyy-MM-dd',
                'options' => [
                    'class' => 'form-control col-lg-3', 'placeholder' => "默认今天",
                ],
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'todayBtn' => true,
                ]
            ])->label('完成结束时间');
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-6" >
            <?php echo $form->field($model, 'username')->textInput()->label('用户名') ?>
        </div>
        <div class="col-xs-6">
            <?php echo $form->field($model, 'department')->dropDownList(ArrayHelper::map(backend\models\Orders::findDepartment($model->company_id)->orderBy('id asc')->all(), 'id', 'name'), ['prompt' => '请选择部门'])->label('部门'); ?>
        </div>
        <div class="col-xs-6" >
            <?php echo $form->field($model, 'name')->textInput()->label('姓名') ?>
        </div>
        <div class="col-xs-6">
                    <?php echo $form->field($model, 'pay_status')->dropDownList(['1'=>'未支付','2'=>'已支付'],['prompt'=>'全部'])->label('支付状态')?>
        </div>
    </div>
    <div class="form-group" style="margin-left:40%">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary', 'name' => 'select', 'value' => 'select', 'id' => 'select']) ?>
        <?php ActiveForm::end(); ?>
        <div style="display:inline">
            <?= Html::submitButton('导出', ['class' => 'btn btn-success', 'name' => 'export', 'value' => 'export']) ?>
        </div>

</div>