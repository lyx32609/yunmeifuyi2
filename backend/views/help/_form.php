<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Help */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="help-form">

    <?php $form = ActiveForm::begin(); ?>

    <label class="control-label" for="help-parent_id">顶级分类</label>
    <select id="help-type" class="form-control" name="Help[type]"">
        <option value="">请选择分类</option>
        <option value="1">注意事项</option>
        <option value="2">使用须知</option>
    </select>
    <!--parent_id 为0，表示为顶级类型-->
    <?= $form->field($model,'parent_id')->textInput()->hiddenInput(['value'=>0])->label(false) ?>
    <?= $form->field($model,'son_id')->textInput()->hiddenInput(['value'=>0])->label(false) ?>
    <?= $form->field($model, 'content')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>



