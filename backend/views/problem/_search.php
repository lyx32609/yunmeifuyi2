<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Regions;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\problemSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<script> 
$(function(){
    getall();
    <?php  if(!$model->area){?>
        $('.field-problemsearch-city').css('display','none');
    <?php }?>
    <?php  if(!$model->city){?>
        $('.field-problemsearch-company_id').css('display','none');
    <?php }?>
    
})
function getall(){
    //getDepartment();
}
function getCity()
{
    var domainid=$('#problemsearch-area').val() ? $('#problemsearch-area').val() : 0;
    $.ajax({
        type: "GET",
        url: "/user-sign/get-city?id="+domainid+"&pid=<?php echo $_GET['id'] ?? ''; ?>",
        async:false,
        success: function(data){
            $("select#problemsearch-city").html(data);
            $('.field-problemsearch-city').css('display','block');
        }
    });
} 

//获取公司
function getCompany()
{
    var area_id = $('#problemsearch-area').val() ? $('#problemsearch-area').val() : 0;
    var city_id = $('#problemsearch-city').val() ? $('#problemsearch-city').val() : 0;
        $.ajax({
        type: "GET",
        url: "/user-sign/get-company?area_id="+area_id+"&city_id="+city_id,
        async:false,
        success: function(data){
            console.log(data);
            $("select#problemsearch-company_id").html(data);
            $('.field-problemsearch-company_id').css('display','block');
        }
    });
}

//获取部门
function  getDepartment()
{
        var area_id = $('#problemsearch-area').val() ? $('#problemsearch-area').val() : 0;
        var city_id = $('#problemsearch-city').val() ? $('#problemsearch-city').val() : 0;
        var company_id = $('#problemsearch-company_id').val() ? $('#problemsearch-company_id').val() : 0;
        $.ajax({
        type: "GET",
        url: "/user-sign/get-department?area_id="+area_id+"&domain_id="+city_id+"&company_id="+company_id,
        async:false,
        success: function(data){
            console.log(data);
            $("select#problemsearch-department_id").html(data);
        }
    });
}
</script>
<div class="problem-search">

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
        <div class="row" >
            <?php if((Yii::$app->user->identity->rank == 30) || (in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))){?>
            <div class="col-xs-6" >
            <?php 
            $domain_id = Yii::$app->user->identity->domain_id;
            $city = Regions::find()
                    ->select(["p_region_id","local_name"])
                    ->where(["region_id" => $domain_id])
                    ->one();
            $area = Regions::find()
                    ->select(["region_id","local_name"])
                    ->where(["region_id" => $city['p_region_id']])
                    ->one();
            $area_id = $area["region_id"];
            ?>
                    <?= $form->field($model,'area')->dropDownList(ArrayHelper::map(backend\models\Regions::findProvince()->all(),'region_id','local_name' ),['prompt'=>"请选择省",'onchange'=>'getCity();',])->label('省') ?>
                    <?= $form->field($model,'city')->dropDownList(ArrayHelper::map(backend\models\Regions::findCity($model->area)->all(),'region_id','local_name' ),['prompt'=>"请选择市",'onchange'=>'getCompany();',])->label('市') ?>
                    <?= $form->field($model,'company_id')->dropDownList(ArrayHelper::map(backend\models\CompanyCategroy::findCompany($model->area,$model->city)->orderBy('id asc')->all(),'id','name' ),['prompt'=>"请选择公司",'onchange'=>'getDepartment();',])->label('公司') ?>
            </div>
            <?php }
            $rules = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id));//角色hr可以看部门
            if(in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))
            {?>
            <div class="col-xs-6" >
                <?php 
                    echo  $form->field($model,'department_id')->dropDownList(ArrayHelper::map(backend\models\UserDepartment::findDepartment($model->area,$model->city,$model->company_id)->orderBy('id asc')->all(),'id','name' ),['prompt'=>'请选择部门'])->label('部门'); ?>
            </div>
            <?php }if((Yii::$app->user->identity->rank == 30) || in_array('hr',$rules) || (Yii::$app->user->identity->rank == 3))
                {?>
                    <div class="col-xs-6" >
                    <?php echo  $form->field($model,'department_id')->dropDownList(ArrayHelper::map(backend\models\UserDepartment::findDepartment($model->area,$model->city,$model->company_id)->orderBy('id asc')->all(),'id','name' ),['prompt'=>'请选择部门'])->label('部门') ;?>
                    </div>
                <?php }?>
        
            <div class="col-xs-6" >
                    <?php echo $form->field($model, 'priority')->dropDownList(['1'=>'一级','2'=>'二级',3=>'三级'],['prompt'=>'全部'])->label('优先级')?>
            </div>
            <div class="col-xs-6" >
                <?php echo $form->field($model, 'user_name')->textInput()->label('创建人') ?>
            </div>
            <div class="col-xs-6" >
                <?php echo $form->field($model, 'problem_title')->textInput()->label('问题标题') ?>
            </div>
    </div>  
    
    <div class="form-group" style="margin-left:40%">
        <?php //Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary','name'=>'select','value'=>'select','id'=>'select1']) ?>
        <div style="display:inline"><?= Html::submitButton('导出', ['class' => 'btn btn-success','name'=>'export','value'=>'export']) ?>  
        </div>
        
    </div>
    <?php ActiveForm::end(); ?>
</div>