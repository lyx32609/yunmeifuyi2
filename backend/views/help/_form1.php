<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\Help */
/* @var $form yii\widgets\ActiveForm */
?>
<script>

    function getSecond()
    {
        var type=$('#help-type').val() ? $('#help-type').val() : 0;
//        console.log(type);
        $.ajax({
            type: "GET",
            url: "/help/get-second?type="+type,
            async:false,
            success: function(data){
                console.log(data);
                $('.field-help-parent_id').css('display','block');
                $("select#help-parent_id").html(data);
            }
        });
    }
</script>

<div class="help-form">

    <?php $form = ActiveForm::begin(); ?>

    <label class="control-label" for="help-type">顶级分类</label>
    <select id="help-type" class="form-control" name="Help[type]" onchange="getSecond();">
    <option value="">请选择分类</option>
    <option value="1">注意事项</option>
    <option value="2">使用须知</option>
    </select>

    <?= $form->field($model,'parent_id')->dropDownList(ArrayHelper::map(backend\models\Help::findSecond($model->id)->all(),'id','content' ),['prompt'=>"请选择一级分类"])->label('一级分类') ?>

    <?= $form->field($model,'type')->textInput()->hiddenInput(['value'=>0])->label(false) ?>
    <?= $form->field($model,'son_id')->textInput()->hiddenInput(['value'=>0])->label(false) ?>

    <?= $form->field($model, 'content')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
