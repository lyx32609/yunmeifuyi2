<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model backend\models\Help */
/* @var $form yii\widgets\ActiveForm */
?>
<script>
    window.onload=function(){
        $("#help-content").css('min-height','400px');
    }

    function getThird()
    {
        var type=$('#help-parent_id').val() ? $('#help-parent_id').val() : 0;
        console.log(type);
        $.ajax({
            type: "GET",
            url: "/help/get-third?type="+type,
            async:false,
            success: function(data){
                $('.field-help-son_id').css('display','block');
                $("select#help-son_id").html(data);

            }

        });
    }
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
<!--创建三级分类-->
<div class="help-form">

    <?php $form = ActiveForm::begin(); ?>
    <!--一级分类-->
    <label class="control-label" for="help-type">顶级分类</label>
    <select id="help-type" class="form-control" name="Help[type]" onchange="getSecond();">
        <option value="0">请选择分类</option>
        <option value="1">注意事项</option>
        <option value="2">使用须知</option>
    </select>
    <!--二级分类-->
    <?= $form->field($model,'parent_id')->dropDownList(ArrayHelper::map(backend\models\Help::findSecond($model->id)->all(),'id','content' ),['prompt'=>"请选择二级分类",'onchange'=>'getThird();'])->label('二级分类') ?>

    <!--三级分类-->
    <?= $form->field($model,'son_id')->dropDownList(ArrayHelper::map(backend\models\Help::findThird($model->id)->all(),'id','content' ),['prompt'=>"请选择三级分类"])->label('三级分类') ?>

    <?= $form->field($model,'type')->textInput()->hiddenInput(['value'=>0])->label(false) ?>
    <?= $form->field($model,'parent_id')->textInput()->hiddenInput(['value'=>0])->label(false) ?>

    <?= $form->field($model, 'content')->widget('common\widgets\ueditor\Ueditor',[
            'options'=>[
                'initialFrameWidth' => "100%",
            ]
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? '创建' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
