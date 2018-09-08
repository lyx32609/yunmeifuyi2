<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CompanyCategroySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="company-categroy-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'createtime') ?>

    <?= $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'area_id') ?>

    <?php // echo $form->field($model, 'domain_id') ?>

    <?php // echo $form->field($model, 'fly') ?>

    <?php // echo $form->field($model, 'type') ?>

    <?php // echo $form->field($model, 'review') ?>

    <?php // echo $form->field($model, 'license_num') ?>

    <?php // echo $form->field($model, 'register_money') ?>

    <?php // echo $form->field($model, 'business') ?>

    <?php // echo $form->field($model, 'business_ress') ?>

    <?php // echo $form->field($model, 'staff_num') ?>

    <?php // echo $form->field($model, 'acting') ?>

    <?php // echo $form->field($model, 'proxy_level') ?>

    <?php // echo $form->field($model, 'service_area') ?>

    <?php // echo $form->field($model, 'distribution_merchant') ?>

    <?php // echo $form->field($model, 'distribution_car') ?>

    <?php // echo $form->field($model, 'distribution_staff') ?>

    <?php // echo $form->field($model, 'goods_num') ?>

    <?php // echo $form->field($model, 'failure') ?>

    <?php // echo $form->field($model, 'goods_type') ?>

    <?php // echo $form->field($model, 'service_type') ?>

    <?php // echo $form->field($model, 'product_type') ?>

    <?php // echo $form->field($model, 'salas_business') ?>

    <?php // echo $form->field($model, 'license_image') ?>

    <?php // echo $form->field($model, 'user_image_negative') ?>

    <?php // echo $form->field($model, 'user_image_positive') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
