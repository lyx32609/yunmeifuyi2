<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\ArrayHelper;
use backend\models\CompanyCategroy;
use backend\models\UserDepartment;
use backend\models\Regions;

/* @var $this yii\web\View */
/* @var $model backend\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<script> 
$(function(){
    //getall();
    <?php  if(!$model->area){?>
        $('.field-usersearch-city').css('display','none');
    <?php }?>
    // <?php  if(!$model->city){?>
    //     $('.field-usersearch-company_categroy_id').css('display','none');
    // <?php }?>
    
})
// function getall(){
//     getDepartment();
// }
function getCity()
{
    var domainid=$('#usersearch-area').val() ? $('#usersearch-area').val() : 0;
    $.ajax({
        type: "GET",
        url: "/user-sign/get-city?id="+domainid+"&pid=<?php echo $_GET['id'] ?? ''; ?>",
        async:false,
        success: function(data){
            $("select#usersearch-city").html(data);
            $('.field-usersearch-city').css('display','block');
        }
    });
} 

//获取公司
function getCompany()
{
    var area_id = $('#usersearch-area').val() ? $('#usersearch-area').val() : 0;
    var city_id = $('#usersearch-city').val() ? $('#usersearch-city').val() : 0;
        $.ajax({
        type: "GET",
        url: "/user-sign/get-company?area_id="+area_id+"&city_id="+city_id,
        async:false,
        success: function(data){
            console.log(data);
            $("select#usersearch-company_categroy_id").html(data);
            $('.field-usersearch-company_categroy_id').css('display','block');
        }
    });
}

//获取部门
function  getDepartment()
{
        var area_id = $('#usersearch-area').val() ? $('#usersearch-area').val() : 0;
        var city_id = $('#usersearch-city').val() ? $('#usersearch-city').val() : 0;
        var company_id = $('#usersearch-company_categroy_id').val() ? $('#usersearch-company_categroy_id').val() : 0;
        $.ajax({
        type: "GET",
        url: "/user-sign/get-department?area_id="+area_id+"&domain_id="+city_id+"&company_id="+company_id,
        async:false,
        success: function(data){
            console.log(data);
            $("select#usersearch-department").html(data);
        }
    });
}
</script>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class' => 'form-horizontal'],
        'fieldConfig' => [
            'template' => "
            <div class='col-xs-2 col-sm-3 text-right'>{label}</div>
            <div class='col-xs-8 col-sm-7'>{input}</div>
            <div class='col-xs-11 col-xs-offset-3 col-sm-2 col-sm-offset-0'>{error}</div>",
        ]
    ]); 
    $rank = Yii::$app->user->identity->rank;
    $rules = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id));//角色hr可以看部门
    $through = Yii::$app->params['through'];
    $identy_user_id = Yii::$app->user->identity->id;
    $identy_user_companyid = Yii::$app->user->identity->company_categroy_id;
    $identy_fly = CompanyCategroy::findOne($identy_user_companyid)->fly;
    ?>
        <div class="row" >
            <?php if(($rank == 30) || (in_array($identy_user_id, $through))){?>
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
                    <?= $form->field($model,'company_categroy_id')->dropDownList(ArrayHelper::map(backend\models\CompanyCategroy::findCompany($model->area,$model->city)->orderBy('id asc')->all(),'id','name' ),['prompt'=>"请选择公司",'onchange'=>'getDepartment();',])->label('公司') ?>
            </div>
            <?php }
            if(in_array($identy_user_id, $through))
            {?>
            <div class="col-xs-6" >
                <?php 
                    echo  $form->field($model,'department')->dropDownList(ArrayHelper::map(backend\models\UserDepartment::findDepartment($model->area,$model->city,$model->company_id)->orderBy('id asc')->all(),'id','name' ),['prompt'=>'请选择部门'])->label('部门'); ?>
            </div>
            <?php }if(($rank == 30) || in_array('hr',$rules) || ($rank == 3))
                {?>
                    <div class="col-xs-6" >
                    <?php echo  $form->field($model,'department')->dropDownList(ArrayHelper::map(backend\models\UserDepartment::findDepartment($model->area,$model->city,$model->company_id)->orderBy('id asc')->all(),'id','name' ),['prompt'=>'请选择部门'])->label('部门') ;?>
                    </div>
                <?php }?>
        
            <div class="col-xs-6" >
                <?php echo $form->field($model, 'phone')->textInput()->label('联系电话') ?>
            </div>
            <div class="col-xs-6" >
                <?php echo $form->field($model, 'name')->textInput()->label('姓名') ?>
            </div>
            <div class="col-xs-6" >
                <?php echo $form->field($model, 'username')->textInput()->label('用户名') ?>
            </div>
            <?php if((in_array($identy_user_id, $through)) || ($rank == 30) || (($identy_fly == 0) && in_array('hr',$rules))){?>
                <div class="col-xs-6" >
                    <?php echo $form->field($model, 'rank')->dropDownList(['1'=>'一线员工','4'=>'部门经理','3'=>'子公司经理','30'=>'主公司经理'],['prompt'=>'全部'])->label('职务级别') ?>
                </div>
            <?php }?>
            <?php if(($rank == 3) || (($identy_fly != 0) && in_array('hr',$rules))){?>
                <div class="col-xs-6" >
                    <?php echo $form->field($model, 'rank')->dropDownList(['1'=>'一线员工','4'=>'部门经理','3'=>'子公司经理'],['prompt'=>'全部'])->label('职务级别') ?>
                </div>
            <?php }?>
            <?php if(in_array($identy_user_id, $through)){?>
            <div class="col-xs-6" >
                <?php echo $form->field($model, 'item_name')->dropDownList(['admin'=>'admin','staff'=>'staff','deliver'=>'deliver','hr'=>'hr'],['prompt'=>'全部'])->label('权限') ?>
            </div>
            <?php }?>
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
</script>
