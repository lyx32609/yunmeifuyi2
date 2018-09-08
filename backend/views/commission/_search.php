<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $model backend\models\OrdersSearch */
/* @var $form yii\widgets\ActiveForm */

//获取当前登录人的id
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
    </div>

    <div class="row">
        <div class="col-xs-6" >
            <?php echo $form->field($model, 'username')->textInput()->label('账号') ?>
        </div>
        <div class="col-xs-6">
            <?php echo $form->field($model, 'department')->dropDownList(ArrayHelper::map(backend\models\Orders::findDepartment($model->company_id)->orderBy('id asc')->all(), 'id', 'name'), ['prompt' => '请选择部门'])->label('部门'); ?>
        </div>
        <div class="col-xs-6" >
            <?php echo $form->field($model, 'name')->textInput()->label('姓名') ?>
        </div>
        <div class="col-xs-6">
            <div class="form-group field-orderssearch-check_status">

                <div class="col-xs-2 col-sm-3 text-right"><label class="control-label" for="orderssearch-status">状态</label></div>
                <div class="col-xs-8 col-sm-7"><select id="orderssearch-check_status" class="form-control" name="OrdersSearch[check_status]">
                        <option value="0">请选择状态</option>
                        <option value="0">全部</option>
                        <option value="1">未审</option>
                        <option value="2">已审</option>
                    </select>
                </div>
                <div class="col-xs-11 col-xs-offset-3 col-sm-2 col-sm-offset-0">
                    <div class="help-block">

                    </div>
                </div>
            </div>
        </div>


    </div>
    <div class="form-group" style="margin-left:40%">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary', 'name' => 'select', 'value' => 'select', 'id' => 'select']) ?>
        <?php ActiveForm::end(); ?>
        <div style="display:inline">
            <?= Html::submitButton('导出', ['class' => 'btn btn-success', 'name' => 'export', 'value' => 'export']) ?>
        </div>
    </div>