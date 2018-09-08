<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\UserGroup */
/* @var $form yii\widgets\ActiveForm */
?>
<script>
$(function(){
	getDepartment();
});

function getDepartment(){
    var domainid=$('#usergroup-domain_id').val();
    $.post("/user-group/department?id="+domainid+"&pid=<?php echo $_GET['id'] ?? ''; ?>",function(data){
       // $("select#usergroup-department_id").html(data);
      });
}
</script>
<div class="user-group-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'desc')->textInput(['maxlength' => true]) ?>

    <!--     
    <?php //echo  $form->field($model,'domain_id')->dropDownList(ArrayHelper::map(backend\models\UserDomain::findDomin()->all(),'domain_id','region')) ?>
    <?=$form->field($model,'department_id')->dropDownList(ArrayHelper::map(backend\models\UserDepartment::find()->all(),'id','name'))->label('部门')?>
 -->
    
    
    <!--      -->
    <?php //echo $form->field($model, 'domain_id')->dropDownList(ArrayHelper::map(backend\models\UserDomain::findDomin()->all(),'domain_id','region'),[ 'onchange'=>'getDepartment()', ]) ?>
    <?= $form->field($model,'department_id')->dropDownList(ArrayHelper::map(backend\models\UserDepartment::findall(1)->all(),'id','name'))->label('部门')?>

    <?= $form->field($model, 'priority')->textInput(['maxlength' => true]) ?>

	<?= $form->field($model,'is_select')->dropDownList([0=>'不统计',1=>'统计'])->label('是否统计业务数据')?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
