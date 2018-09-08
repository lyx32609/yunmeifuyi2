<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\CompanyCategroy;
use backend\models\UserDepartment;
use backend\models\Regions;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\ShopNoteSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<script> 
$(function(){
    getall();
    <?php  if(!$model->area){?>
        $('.field-shopnotesearch-city').css('display','none');
    <?php }?>
    <?php  if(!$model->city){?>
        $('.field-shopnotesearch-company_id').css('display','none');
    <?php }?>
    
})
function getall(){
    //getDepartment();
}
function getCity()
{
    var domainid=$('#shopnotesearch-area').val() ? $('#shopnotesearch-area').val() : 0;
    $.ajax({
        type: "GET",
        url: "/user-sign/get-city?id="+domainid+"&pid=<?php echo $_GET['id'] ?? ''; ?>",
        async:false,
        success: function(data){
            $("select#shopnotesearch-city").html(data);
            $('.field-shopnotesearch-city').css('display','block');
        }
    });
} 

//获取公司
function getCompany()
{
    var area_id = $('#shopnotesearch-area').val() ? $('#shopnotesearch-area').val() : 0;
    var city_id = $('#shopnotesearch-city').val() ? $('#shopnotesearch-city').val() : 0;
        $.ajax({
        type: "GET",
        url: "/user-sign/get-company?area_id="+area_id+"&city_id="+city_id,
        async:false,
        success: function(data){
            console.log(data);
            $("select#shopnotesearch-company_id").html(data);
            $('.field-shopnotesearch-company_id').css('display','block');
        }
    });
}

//获取部门
function  getDepartment()
{
        var area_id = $('#shopnotesearch-area').val() ? $('#shopnotesearch-area').val() : 0;
        var city_id = $('#shopnotesearch-city').val() ? $('#shopnotesearch-city').val() : 0;
        var company_id = $('#shopnotesearch-company_id').val() ? $('#shopnotesearch-company_id').val() : 0;
        $.ajax({
        type: "GET",
        url: "/user-sign/get-department?area_id="+area_id+"&domain_id="+city_id+"&company_id="+company_id,
        async:false,
        success: function(data){
            console.log(data);
            $("select#shopnotesearch-department").html(data);
        }
    });
}
</script>
<div class="shop-note-search">

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
            $rank = Yii::$app->user->identity->rank;
            if (in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])) {
                ?>
                <div class="col-xs-6">
                    <?php
                    echo $form->field($model, 'department')->dropDownList(ArrayHelper::map(backend\models\UserDepartment::findDepartment($model->area, $model->city, $model->company_id)->orderBy('id asc')->all(), 'id', 'name'), ['prompt' => '请选择部门'])->label('部门'); ?>
                </div>
            <?php }
            if ((Yii::$app->user->identity->rank == 30) || in_array('hr', $rules) || ($rank == 3)){
                ?>
                    <div class="col-xs-6" >
                    <?php echo  $form->field($model,'department')->dropDownList(ArrayHelper::map(backend\models\UserDepartment::findDepartment($model->area,$model->city,$model->company_id)->orderBy('id asc')->all(),'id','name' ),['prompt'=>'请选择部门'])->label('部门') ;?>
                    </div>
                <?php }?>
        
            <div class="col-xs-6" >
                <?php echo $form->field($model, 'name',['inputOptions'=>['placeholder'=>"姓名重复或一人多帐号时请使用帐号查询",'class'=>"form-control"]])->textInput()->label('姓名') ?>
            </div>
            <div class="col-xs-6" >
                <?php echo $form->field($model, 'username')->textInput()->label('账号') ?>
            </div>
    </div>  
    
    <div class="form-group" style="margin-left:40%">
        <?php //Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary','name'=>'select','value'=>'select','id'=>'select1']) ?>
        <div style="display:inline"><?= Html::submitButton('导出', ['class' => 'btn btn-success','name'=>'export','value'=>'export']) ?>  
        </div>
        
        <?php ActiveForm::end(); ?>
    </div>

</div>
<script type="text/javascript" >
    $("#export").click(function(){
        var start_time=$("#shopnotesearch-start_time").val();
        var end_time=$("#shopnotesearch-end_time").val();   
        var username=$("#shopnotesearch-username").val();
        var type=$("#shopnotesearch-type").val();
        $.post('/shop-note/export',{start_time:start_time,end_time:end_time,username:username,type:type},function(data){
                
            })
        
        })
//    $.ajax({
//      url: '/shop-note/export',
//      dataType:"json",
//      data:{start_time:start_time,end_time:end_time,username:username,type:type},
//      type:'POST',
//      async:true,
//      beforeSend:function(){
//          $("body").showLoading();
//          },
//          //下面就是获取到的下载地址，直接通过document.location函数获取下载
//      success:function(output){
//          $("body").hideLoading();
//          alert(output.url);
//          document.location.href =("data/"+output.url);
//        },
//      error: function(){
//          $("body").hideLoading();
//          sweetAlert("错误", "导出excel出错!", "error");
//          },
//  });
</script>
