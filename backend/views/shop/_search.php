<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ShopSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="shop-search">

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
            ])->label('开始时间');
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
            ])->label('结束时间');
            ?>
        </div>
        <div class="col-xs-6">
            <?php  echo $form->field($model, 'shop_name') ?>
        </div>
        <div class="col-xs-6">
            <?php  echo $form->field($model, 'name') ?>
        </div>
        <div class="col-xs-6">
            <?php  echo $form->field($model, 'user_name') ?>
        </div>
        <div class="col-xs-6">
            <?php  echo $form->field($model, 'shop_addr') ?>
        </div>
        <div class="col-xs-6">
            <?php  echo $form->field($model, 'phone') ?>
        </div>
        <div class="col-xs-6">
            <?php  echo $form
                ->field($model, 'shop_type')
                ->dropDownList(['0'=>'请选择','1'=>'生产商','2'=>'供货商',
                '3'=>'采购商','4'=>'配送商','5'=>'店铺商','6'=>'运营商','7'=>'销售商','8'=>'服务商'])
                ->label('客户类型') ?>
        </div>
    </div>
    <div class="form-group" style="margin-left:40%">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary','name'=>'select','value'=>'select']) ?>

        <div style="display:inline">
            <?= Html::submitButton('导出', ['class' => 'btn btn-success', 'name' => 'export', 'value' => 'export']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <?php //echo $form->field($model, 'id') ?>

    <?php // echo $form->field($model, 'shop_name') ?>

    <?php // echo $form->field($model, 'name') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'shop_type') ?>

    <?php // echo $form->field($model, 'shop_source') ?>

    <?php // echo $form->field($model, 'shop_status') ?>

    <?php // echo $form->field($model, 'shop_priority') ?>

    <?php // echo $form->field($model, 'shop_longitude') ?>

    <?php // echo $form->field($model, 'shop_latitude') ?>

    <?php // echo $form->field($model, 'shop_image') ?>

    <?php // echo $form->field($model, 'user_name') ?>

    <?php // echo $form->field($model, 'user_id') ?>

    <?php // echo $form->field($model, 'company_category_id') ?>

    <?php // echo $form->field($model, 'shop_review') ?>

    <?php // echo $form->field($model, 'shop_addr') ?>

    <?php // echo $form->field($model, 'shop_domain') ?>

    <?php // echo $form->field($model, 'createtime') ?>

    <?php // echo $form->field($model, 'shop_title') ?>

    <?php // echo $form->field($model, 'shop_describe') ?>





</div>
