<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserLogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-log-search">

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
        <div class="col-xs-6" >
            <?php
            echo $form->field($model, 'start_time')->widget(\yii\jui\DatePicker::classname(), [
                'language' => 'zh-CN',
                'dateFormat' => 'yyyy-MM-dd',
                'options'=>[
                    'class' => 'form-control col-lg-3',  'placeholder' => "默认一周前",
                ],
                'clientOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'todayBtn' => true,
                ]
        ])->label('开始时间');
        ?> 
        </div>
        <div class="col-xs-6" >
            <?php
            echo $form->field($model, 'end_time')->widget(\yii\jui\DatePicker::classname(), [
                'language' => 'zh-CN',
                'dateFormat' => 'yyyy-MM-dd',
                'options'=>[
                    'class' => 'form-control col-lg-3',  'placeholder' => "默认今天",
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
            <?php echo $form->field($model, 'name')->textInput()->label('姓名') ?>
        </div>
        <div class="col-xs-6" >
            <?php echo $form->field($model, 'username')->textInput()->label('用户名') ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6" >
            <?php echo $form->field($model, 'type')->dropDownList(['1'=>'修改店铺定位坐标','2'=>'登录/退出后台','3' => '用户管理模块'],['prompt'=>'全部'])->label('操作类型')?>
        </div>
    </div>
    <div class="form-group" style="margin-left:40%">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary','name'=>'select','value'=>'select','id'=>'select1']) ?>
        <?php ActiveForm::end(); ?>
    </div>

</div>
