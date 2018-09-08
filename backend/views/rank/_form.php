<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\UserIndex */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-index-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'userid')->textInput() ?>

    <?= $form->field($model, 'visitingnum')->textInput() ?>

    <?= $form->field($model, 'registernum')->textInput() ?>

    <?= $form->field($model, 'ordernum')->textInput() ?>

    <?= $form->field($model, 'orderamount')->textInput() ?>

    <?= $form->field($model, 'orderuser')->textInput() ?>

    <?= $form->field($model, 'deposit')->textInput() ?>

    <?= $form->field($model, 'maimaijinorder')->textInput() ?>

    <?= $form->field($model, 'maimaijinamount')->textInput() ?>

    <?= $form->field($model, 'maimaijinuser')->textInput() ?>

    <?= $form->field($model, 'inputtime')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
