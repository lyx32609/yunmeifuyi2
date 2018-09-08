<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CompanyCategroyReview */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="company-categroy-review-form">
<table id="w0" class="table table-striped table-bordered detail-view">
    <tbody>
    	<tr><th>名称</th><td>填写值</td><td>审核理由</td></tr>
    	<tr><th></th><td></td><td></td></tr>
        <?php $form = ActiveForm::begin(); ?>
        <tr><th>企业名称</th><th><?php echo $model_company->name;?></th><th><?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?></th></tr>
	</tbody>
</table>

    

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'createtime')->textInput() ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'area_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'domain_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'fly')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'review')->textInput() ?>

    <?= $form->field($model, 'license_num')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'register_money')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'business')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'business_ress')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'staff_num')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'acting')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'proxy_level')->textInput() ?>

    <?= $form->field($model, 'service_area')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'distribution_merchant')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'distribution_car')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'distribution_staff')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'goods_num')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'failure')->textInput() ?>

    <?= $form->field($model, 'goods_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'service_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'product_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'salas_business')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'license_image')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_image_negative')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_image_positive')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>