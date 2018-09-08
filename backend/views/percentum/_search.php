<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\PercentumSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="percentum-search">

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
            <?= $form->field($model, 'username') ?>
        </div>
        <div class="col-xs-6">
            <?= $form->field($model, 'name') ?>
        </div>
        <div class="col-xs-6">
            <?= $form->field($model, 'department_id')->dropDownList(ArrayHelper::map(\backend\models\Percentum::findDepartment()->all(),'id','name'),['prompt'=>"请选择部门"]) ?>
        </div>
    </div>
    <div class="form-group" style="margin-left:30%">
        <?= Html::submitButton(Yii::t('app', '查询修改记录'), ['class' => 'btn btn-primary','name'=>'select','value'=>'select']) ?>
        <div style="display:inline">
            <?= Html::submitButton('导出修改记录', ['class' => 'btn btn-success', 'name' => 'export', 'value' => 'export']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>