<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserIndexSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-index-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php // $form->field($model, 'id') ?>

    <?php // $form->field($model, 'userid') ?>

    <?php // $form->field($model, 'visitingnum') ?>

    <?php // form->field($model, 'registernum') ?>

    <?php // $form->field($model, 'ordernum') ?>

    <?php  //echo $form->field($model, 'orderamount') ?>

    <?php  //echo $form->field($model, 'orderuser') ?>

    <?php  //echo $form->field($model, 'deposit') ?>

    <?php  //echo $form->field($model, 'maimaijinorder') ?>

    <?php  //echo $form->field($model, 'maimaijinamount') ?>

    <?php  //echo $form->field($model, 'maimaijinuser') ?>

    <?php  //echo $form->field($model, 'inputtime') ?>

    <div class="form-group">
        <?php //echo Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php //echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
