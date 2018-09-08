<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\CompanyInterface */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-interface-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'company_id')->dropDownList(ArrayHelper::map(backend\models\CompanyCategroy::find()->all(),'id','name',"type"))->label('所属公司') ?>

    <?= $form->field($model, 'url')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'public_key')->label('公钥')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'privace_key')->label('秘钥')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'module_id')->dropDownList(ArrayHelper::map(backend\models\CompanyInterfaceModule::find()->all(),'id',"module_name"))->label('所属模块')  ?>

    <?= $form->field($model, 'protocol')->label('安全协议')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '新增' : '修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
