<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\CompanyCategroy;
use backend\models\Regions;
/* @var $this yii\web\View */
/* @var $model backend\models\petitionsearchSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<script>
    function getCity()
    {
        var domainid=$('#petitionsearch-province').val() ? $('#petitionsearch-province').val() : 0;
        console.log(domainid);
        $.ajax({
            type: "GET",
            url: "/user-sign/get-city?id="+domainid+"&pid=<?php echo $_GET['id'] ?? ''; ?>",
            async:false,
            success: function(data){
                $('.field-petitionsearch-domain_id').css('display','block');
                $("select#petitionsearch-domain_id").html(data);
            }
        });
    }
    //获取公司
    function getCompany()
    {
        var area_id = $('#petitionsearch-province').val() ? $('#petitionsearch-province').val() : 0;
        var city_id = $('#petitionsearch-domain_id').val() ? $('#petitionsearch-domain_id').val() : 0;
        $.ajax({
            type: "GET",
            url: "/user-sign/get-company-name?area_id="+area_id+"&city_id="+city_id,
            async:false,
            success: function(data){
                $('.field-petitionsearch-company_categroy_id').css('display','block');
                $("select#petitionsearch-company_categroy_id").html(data);
            }
        });
    }
    //获取部门
    function  getDepartment()
    {
        var area_id = $('#petitionsearch-province').val() ? $('#petitionsearch-province').val() : 0;
        var city_id = $('#petitionsearch-domain_id').val() ? $('#petitionsearch-domain_id').val() : 0;
        var company_id = $('#petitionsearch-company_categroy_id').val() ? $('#petitionsearch-company_categroy_id').val() : 0;
        $.ajax({
            type: "GET",
            url: "/user-sign/get-department-name?area_id="+area_id+"&city_id="+city_id+"&company_id="+company_id,
            async:false,
            success: function(data){
                $('.field-petitionsearch-department_id').css('display','block');
                $("select#petitionsearch-department_id").html(data);
            }
        });
    }
</script>
<div class="petitionsearch-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => ['class' => 'form-horizontal','autocomplete'=>"off"],
        'fieldConfig' => [
            'template' => "
            <div class='col-xs-2 col-sm-3 text-right'>{label}</div>
            <div class='col-xs-8 col-sm-7'>{input}</div>",
        ]
    ]);
    $rank = Yii::$app->user->identity->rank;
    $rules = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id));//角色hr可以看部门
    $through = Yii::$app->params['through'];
    $identy_user_id = Yii::$app->user->identity->id;
    $identy_user_companyid = Yii::$app->user->identity->company_categroy_id;
    $identy_fly = CompanyCategroy::findOne($identy_user_companyid)->fly;
    ?>
    <div class="row">
        <div class="col-xs-6" >
            <?php
            echo $form->field($model, 'start_time')->widget(\yii\jui\DatePicker::classname(), [
                'language' => 'zh-CN',
                'dateFormat' => 'yyyy-MM-dd',
                'options'=>[
                    'class' => 'form-control col-lg-3','placeholder' => "默认一周前",
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
                    'class' => 'form-control col-lg-3','placeholder' => "默认今天",
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

        <?php if(($rank == 30) || (in_array($identy_user_id, $through))){?>
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
    <?php if(($rank == 30) || (in_array($identy_user_id, $through))){?>
    <div class="row" >
            <div class="col-xs-6" >
                <?= $form->field($model,'province')->dropDownList(ArrayHelper::map(backend\models\Regions::findProvince()->all(),'region_id','local_name' ),['prompt'=>"请选择省",'onchange'=>'getCity();',])->label('省') ?>
            </div>
            <div class="col-xs-6" >
            <?= $form->field($model,'domain_id')->dropDownList(ArrayHelper::map(backend\models\Regions::findCity($model->province)->all(),'region_id','local_name' ),['prompt'=>"请选择市",'onchange'=>'getCompany();',])->label('市') ?>
            </div>
    </div>
    <div class="row" >
            <div class="col-xs-6" >
                <?= $form->field($model,'company_categroy_id')->dropDownList(ArrayHelper::map(backend\models\CompanyCategroy::findCompany($model->province,$model->domain_id)->orderBy('id asc')->all(),'id','name' ),['prompt'=>"请选择公司",'onchange'=>'getDepartment();',])->label('公司') ?>
            </div>
            <div class="col-xs-6" >
                 <?= $form->field($model,'department_id')->dropDownList(ArrayHelper::map(backend\models\UserDepartment::findDepartment($model->province,$model->domain_id,$model->company_categroy_id)->orderBy('priority desc')->all(),'id','name' ),['prompt'=>"请选择部门"])->label('部门') ?>
            </div>
    </div>
        <?php } ?>

    <div class="row" >
        <div class="col-xs-6" >
            <?=$form->field($model, 'name')->textInput()->label('姓名') ?>
        </div>
        <div class="col-xs-6" >
            <?=$form->field($model, 'username')->textInput()->label('用户名') ?>
        </div>
    </div>
    <?php } ?>
    <div class="row" >
        <div class="col-xs-6" >
            <?=$form->field($model, 'status')->dropDownList([1=>'审核中',2=>'已完成已支付',3=>'已完成未支付',4=>'已完成',5=>'已作废'],['prompt'=>'全部'])->label('签呈状态') ?>
        </div>
        <div class="col-xs-6" >
            <?=$form->field($model, 'flag')->dropDownList([1=>'发起',2=>'接收'],['prompt'=>'全部'])->label('方向') ?>
        </div>

    </div>
    <div class="form-group" style="margin-left:40%">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary','name'=>'select','value'=>'select','id'=>'select1']) ?>
        &nbsp;&nbsp;
        <?php //echo Html::a('提报签呈', ['create'], ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
