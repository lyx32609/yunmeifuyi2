<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Alert;

/* @var $this yii\web\View */
/* @var $model backend\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<script>
$(function(){
    getall();
    // <?php  if(!$model->province){?>
    //     $('.field-user-domain_id').css('display','none');
    // <?php }?>
    // <?php  if(!$model->domain_id){?>
    //     $('.field-user-company_categroy_id').css('display','none');
    // <?php }?>
    
})
function getall(){
    // getDepartment();
    // getGroup();
}

function getCity()
{
    var domainid=$('#user-province').val() ? $('#user-province').val() : 0;
    $.ajax({
        type: "GET",
        url: "/user-sign/get-city?id="+domainid+"&pid=<?php echo $_GET['id'] ?? ''; ?>",
        async:false,
        success: function(data){
            $('.field-user-domain_id').css('display','block');
            $("select#user-domain_id").html(data);
        }
    });
} 

//获取公司
function getCompany()
{
    var area_id = $('#user-province').val() ? $('#user-province').val() : 0;
    var city_id = $('#user-domain_id').val() ? $('#user-domain_id').val() : 0;
        $.ajax({
        type: "GET",
        url: "/user-sign/get-company?area_id="+area_id+"&city_id="+city_id,
        async:false,
        success: function(data){
            console.log(data);
            $('.field-user-company_categroy_id').css('display','block');
            $("select#user-company_categroy_id").html(data);
        }
    });
}

//获取部门
function  getDepartment()
{
    var area_id = $('#user-province').val() ? $('#user-province').val() : 0;
    var city_id = $('#user-domain_id').val() ? $('#user-domain_id').val() : 0;
    var company_id = $('#user-company_categroy_id').val() ? $('#user-company_categroy_id').val() : 0;
        $.ajax({
        type: "GET",
        url: "/user-sign/get-department?area_id="+area_id+"&domain_id="+city_id+"&company_id="+company_id,
        async:false,
        success: function(data){
            console.log(data);
            $('.field-user-department_id').css('display','block');
            $("select#user-department_id").html(data);
        }
    });
}

function getGroup(){
    var departmentid=$('#user-department_id').val();
    if(departmentid != '-'){
        $.post("/user/group?id="+departmentid+"&pid=<?php echo$_GET['id'] ?? ''; ?>",function(data){
            $("select#user-group_id").html(data);
          });
      }else{
          $("select#user-group_id").html('<option value="0">无分组</option>');
      }
}




</script>
<div class="user-form">
    <?php 
        $rank = Yii::$app->user->identity->rank;
        $through = Yii::$app->params['through'];
        $identy_user_id = Yii::$app->user->identity->id;
        $identy_user_companyid = Yii::$app->user->identity->company_categroy_id;
        $rules = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id));
        //$identy_fly = CompanyCategroy::findOne()
    ?>
    <?php if(Yii::$app->session->hasFlash('info')):?>
            <?php   
           echo Alert::widget([
              'options' => ['class' => 'alert-danger'],
              'body' => Yii::$app->session->getFlash('info'),
           ]);
        ?>
    
    <?php endif;?>
    <?php $form = ActiveForm::begin(); ?>
    <?php if($model->isNewRecord):?>
        <?= $form->field($model, 'username')->textInput(['maxlength' => true])->label('用户名（系统编号）') ?>
    <?php else:?>
        <?= $form->field($model, 'username')->textInput(['maxlength' => true,'disabled'=>true])->label('用户名（系统编号）') ?>
    <?php endif;?>
    <?= $form->field($model, 'staff_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
    <?php 
    //echo $rank;
        /*超级管理员——所有*/
    if(in_array($identy_user_id,$through))
    {?>
        <?= $form->field($model,'rank')->dropDownList(Yii::$app->params['rank']) ?>
        <?= $form->field($model,'is_staff')->dropDownList([1=>'在职',0=>'离职']) ?>
        <?= $form->field($model,'cid')->textInput(['maxlength' => true]) ?>
    <?php }
    if($model->is_staff == 0 && in_array($identy_user_id,$through))
    {?>
        <?= $form->field($model,'dimission_time')->textInput([
        'maxlength' => true,
        'disabled'=>'disabled',
    ]) ?>
    <?php }
        /*主公司经理——子公司经理、部门经理、一线员工*/
        if($rank == 30) 
        {?>
             <?= $form->field($model,'rank')->dropDownList([30 => '主公司经理', 3=>'子公司经理', 4=>'部门经理', 1=>'一线员工']) ?>
             
        <?php $rank = Yii::$app->user->identity->rank;}
        /*子公司经理——可添加部门经理、一线员工*/
        elseif($rank == 3)
        {?>
            <?=  $form->field($model,'rank')->dropDownList([3=>'子公司经理', 4=>'部门经理', 1=>'一线员工']) ?>
        <?php }
        /*hr——可添加部门经理、一线员工*/
        if(($rank == 4) && (in_array('hr',$rules))) 
        {?>
            <?=  $form->field($model,'rank')->dropDownList([4=>'部门经理',1=>'一线员工']) ?>
        <?php } 
        /*部门经理非hr——只能添加一线员工*/
         if(($rank == 4) && !(in_array('hr',$rules)))
         {?>
           <?= $form->field($model,'rank')->textInput()->hiddenInput(['value'=>1])->label(false) ?>
         <?php }?>
    <?php if((in_array($identy_user_id,$through)) || ($rank == 30)){?>
    <?= $form->field($model,'province')->dropDownList(ArrayHelper::map(backend\models\Regions::findProvince()->all(),'region_id','local_name' ),['prompt'=>"请选择省",'onchange'=>'getCity();',])->label('省') ?>

    <?= $form->field($model,'domain_id')->dropDownList(ArrayHelper::map(backend\models\Regions::findCity($model->province)->all(),'region_id','local_name' ),['prompt'=>"请选择市",'onchange'=>'getCompany();',])->label('市') ?>

    <?= $form->field($model,'company_categroy_id')->dropDownList(ArrayHelper::map(backend\models\CompanyCategroy::findCompany($model->province,$model->domain_id)->orderBy('id asc')->all(),'id','name' ),['prompt'=>"请选择公司",'onchange'=>'getDepartment();',])->label('公司') ?>
    <?php }else{?>
        <?= $form->field($model,'domain_id')->textInput()->hiddenInput(['value'=>Yii::$app->user->identity->domain_id])->label(false) ?>
        <?= $form->field($model,'company_categroy_id')->textInput()->hiddenInput(['value'=>Yii::$app->user->identity->company_categroy_id])->label(false) ?>
    <?php }?>
    <?php if(($rank == 4) && !(in_array('hr',$rules))){?>
        <?= $form->field($model,'department_id')->textInput()->hiddenInput(['value'=>Yii::$app->user->identity->department_id])->label(false) ?>
    <?php }else{?>
        <?= $form->field($model,'department_id')->dropDownList(ArrayHelper::map(backend\models\UserDepartment::findDepartment($model->province,$model->domain_id,$model->company_categroy_id)->orderBy('priority desc')->all(),'id','name' ),
            ['prompt'=>"请选择部门",
            'onchange'=>'getGroup() ',
             ]) ?>
    <?php }?>

<?php if(($rank == 4) && !(in_array('hr',$rules))){?>
    <?= $form->field($model,'group_id')->dropDownList(ArrayHelper::map(backend\models\UserGroup::getGroupByDepartment(Yii::$app->user->identity->department_id),'id','name'),[ 'prompt'  =>  '无分组']) ?>
<?php }else{?>
    <?= $form->field($model,'group_id')->dropDownList(ArrayHelper::map(backend\models\UserGroup::find($model->department_id)->orderBy('priority desc')->all(),'id','name'),[ 'prompt'  =>  '无分组']) ?>
<?php }?>

    <?= $form->field($model,'is_select')->dropDownList([1=>'统计',0=>'不统计'])->label('是否统计个人业务数据')?>
    <?php 
        if(in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))
        {
           echo  $form->field($model, 'menuids')->checkboxList(ArrayHelper::map(backend\models\Menus::findMenu()->all(),'id', 'name'))->label('用户菜单权限分配'); 
        }          
    ?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
