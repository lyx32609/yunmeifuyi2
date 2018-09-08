<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $this->beginPage() ?>
<?php $this->beginBody() ?>
<link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
<style>
body {
	width: 100%;
    height: 100%;
    background: #55ade1;
	overflow:hidden;
}
#loginform-verifycode{
	width: 58%;
	float: left;
}
#loginform-verifycode-image{
	float: right;
	width: 120px;
	margin-left: 2%;
	height: 40px;
}
.field-loginform-verifycode{
	overflow: hidden;
}
#load{
	position: absolute;
	top:  50%;
	left: 50%;
	margin-left:  -16px;
	margin-top:  -16px;
	display:  none;
}
</style>
<div class="login-box">
	 <div class="login-logo">
        <!-- <b class = ''>帮助中心</b> -->
      </div><!-- /.login-logo -->
   <div class="login-box-body" style="border-radius:2px 30px"	>
   
		<!-- <p class="login-box-msg">Sign in to start your session</p> -->
<!--    <p>Please fill out the following fields to login:</p>--> 
            <?php $form = ActiveForm::begin([
                'id' => 'login-form',

            ]); ?>
			
            <?= $form->field($model, 'username')->textInput([])->label('用户名') ?>

            <?= $form->field($model, 'password')->passwordInput()->label('密码') ?>

            <?= $form->field($model, 'verifyCode')->label('')->widget(\yii\captcha\Captcha::className(), [
                'captchaAction' => ['/site/captcha'],
                'template' => '{input}{image}',
                'imageOptions'=>['style'=>'cursor:pointer'],
            ]) ?>
			<?php
            
               if(Yii::$app->session->hasFlash('info_rank'))
                   echo '<span style="color:#D94600;">'.Yii::$app->session->getFlash('info_rank').'</span>';
           
            ?>
            <div class="form-group" style="position: relative;">
                <?= Html::submitButton('登  录', ['class' => 'btn btn-primary btn-large btn-block', 'name' => 'login-button','onclick' => 'formSubmit()']) ?>
           		<span id="load"><img src="/static/img/loading.gif"/></span>
            </div>

            <?php ActiveForm::end(); ?>
            
    </div>
</div>
<?php $this->endBody() ?>
<?php $this->endPage() ?>
<script type="text/javascript">
  function formSubmit(){
        var username=$("#loginform-username").val();
        var userpass=$("#loginform-password").val();
        var code=$("#loginform-verifycode").val();
        if(username!=''&&userpass!=''&&code!=''){
            $("#load").show();
        }
    } 
</script>



