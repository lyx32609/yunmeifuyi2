<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\models\SupplierAgent;

/* @var $this yii\web\View */
/* @var $model backend\models\UserDepartment */
/* @var $form yii\widgets\ActiveForm */
?>
<script>
$(function(){
	<?php  if(!$model->domain_id){?>
		$('.field-userdepartment-domain_id').css('display','none');
    <?php }?>
})
function getProvince(){
    var domainid=$('#userdepartment-province').val() ? $('#userdepartment-province').val() : 0;
    $.ajax({
        type: "GET",
        url: "/user-sign/province?id="+domainid+"&pid=<?php echo $_GET['id'] ?? ''; ?>",
        async:false,
        success: function(data){
        	$('.field-userdepartment-domain_id').css('display','block');
        	$("select#userdepartment-domain_id").html(data);
        }
    });
} 
</script>
<div class="user-department-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true])->label('部门') ?>

    <?= $form->field($model, 'desc')->textInput(['maxlength' => true]) ?>

    <?php //echo $form->field($model, 'domain_id')->dropDownList(ArrayHelper::map(\backend\models\UserDomain::findDomin()->all(),'domain_id','region')) ?>
	<?= $form->field($model,'province')->dropDownList(ArrayHelper::map(backend\models\Regions::findProvince()->all(),'region_id','local_name' ),['onchange'=>'getProvince();',])->label('省') ?>
	<?= $form->field($model,'domain_id')->dropDownList(ArrayHelper::map(backend\models\Regions::findCity($model->province)->all(),'region_id','local_name' ))->label('市') ?>


    <?= $form->field($model, 'priority')->textInput(['maxlength' => true]) ?>
    
	<?= $form->field($model,'is_select')->dropDownList([0=>'不统计',1=>'统计'])->label('是否统计业务数据')?>
    
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
