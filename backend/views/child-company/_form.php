<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm; 
use yii\helpers\ArrayHelper;
use backend\models\CompanyGoods;
use backend\models\User;
/* @var $this yii\web\View */
/* @var $model backend\models\CompanyCategroy */
/* @var $form yii\widgets\ActiveForm */
?>

<script>
$(function(){
	<?php  if(!$model->domain_id){?>
		$('.field-companycategroy-domain_id').css('display','none');
    <?php }?>
})
function getProvince(){
    var domainid=$('#companycategroy-area_id').val() ? $('#companycategroy-area_id').val() : 0;
    $.ajax({
        type: "GET",
        url: "/user-sign/province?id="+domainid+"&pid=<?php echo $_GET['id'] ?? ''; ?>",
        async:false,
        success: function(data){
        	$('.field-companycategroy-domain_id').css('display','block');
        	$("select#companycategroy-domain_id").html(data);
        }
    });
} 
</script>



<div class="company-categroy-form user-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); 
        $model_user = new User();
    ?>

    <?= $form->field($model_user, 'username')->textInput(['maxlength' => true])->label('登录用户名') ?>
    <?= $form->field($model_user, 'password')->textInput(['maxlength' => true])->label('密码') ?>
    <?= $form->field($model_user, 'name')->textInput(['maxlength' => true])->label('注册人姓名') ?>
    <?= $form->field($model, 'phone')->textInput(['maxlength' => true])->label('手机号') ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label('公司名称') ?>
	<?= $form->field($model,'status')->dropDownList(['0'=>'运营','1'=>'销售','2'=>'供货','3'=>'配送','4'=>'生产','5'=>'服务'])->label('公司类型') ?>
	<?= $form->field($model,'area_id')->dropDownList(ArrayHelper::map(backend\models\Regions::findProvince()->all(),'region_id','local_name' ),['prompt'=>'全部','onchange'=>'getProvince();',])->label('省') ?>
	<?= $form->field($model,'domain_id')->dropDownList(ArrayHelper::map(backend\models\Regions::findCity($model->area_id)->all(),'region_id','local_name' ))->label('市') ?>
    <?= $form->field($model,'fly')->dropDownList(ArrayHelper::map(backend\models\CompanyCategroy::find(["fly"=>0])->all(),'id','name',"type"))->label('主公司') ?>
    <!-- <?= $form->field($model, 'review')->textInput() ?> -->

    <?= $form->field($model, 'license_num')->label('执照编号')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'register_money')->label('注册资金')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'business')->label('经营面积')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'business_ress')->label('经营地址')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'staff_num')->label('人员数量')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'acting')->label('代理品牌')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'proxy_level')->label('代理级别')->textInput() ?>

    <?= $form->field($model, 'service_area')->label('销售或服务区域')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'distribution_merchant')->label('配送商户')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'distribution_car')->label('配送车辆')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'distribution_staff')->label('配送人员')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_num')->label('商品数量')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'failure')->dropDownList(['0'=>'永久使用','1'=>'试用'])->label('是否永久使用') ?>

    <?= $form->field($model, 'goods_type')->dropDownList(ArrayHelper::map(app\models\CompanyGoods::find()->all(),'id',"goods_name"))->label('商品类型') ?>

    <?= $form->field($model, 'service_type')->dropDownList(ArrayHelper::map(app\models\CompanyService::find()->all(),'id',"service_name"))->label('服务类型') ?>

    <?= $form->field($model, 'product_type')->dropDownList(ArrayHelper::map(app\models\CompanyProduct::find()->all(),'id',"product_name"))->label('服务类型') ?>

    <?= $form->field($model, 'salas_business')->label('服务区域')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'license_image')->label('营业执照照片')->fileInput() ?>

    <?= $form->field($model, 'user_image_negative')->label('注册人身份证正面照片')->fileInput() ?>

    <?= $form->field($model, 'user_image_positive')->label('注册人身份证反面照片')->fileInput() ?> 

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '更新', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
