<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */


$this->context->layout = false;
?>
<div class="site-error" align="center">
    <link href="/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <style>
        span {
            color:#55ade1;
        }
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
            <form id="login-form" action="/site/login" method="post" role="form">
                <input type="hidden" name="_csrf-backend" value="bXBZYTYyeUQhKD4tZ38fEihFDExeH1QSO0ITJkx1MDYpNm0vaVQyJg==">
                <div class="form-group field-loginform-username required">
                    <label class="control-label" for="loginform-username">用户名</label>
                    <input type="text" id="loginform-username" class="form-control" name="LoginForm[username]">

                    <p class="help-block help-block-error"></p>
                </div>
                <div class="form-group field-loginform-password required">
                    <label class="control-label" for="loginform-password">密码</label>
                    <input type="password" id="loginform-password" class="form-control" name="LoginForm[password]">

                    <p class="help-block help-block-error"></p>
                </div>
                <div class="form-group field-loginform-verifycode required">
                    <label class="control-label" for="loginform-verifycode"></label>
                    <input type="text" id="loginform-verifycode" class="form-control" name="LoginForm[verifyCode]"><img id="loginform-verifycode-image" src="/site/captcha?v=599bd8d7ef420" alt="" style="cursor:pointer">

                    <p class="help-block help-block-error"></p>
                </div>			            <div class="form-group" style="position: relative;">
                    <button type="submit" class="btn btn-primary btn-large btn-block" name="login-button" onclick="formSubmit()">登  录</button>           		<span id="load"><img src="/static/img/loading.gif"/></span>
                </div>

            </form>
        </div>
    </div>
    <script src="/assets/c960a0ae/jquery.js"></script>
    <script src="/assets/faa195a7/yii.js"></script>
    <script src="/assets/faa195a7/yii.validation.js"></script>
    <script src="/assets/faa195a7/yii.captcha.js"></script>
    <script src="/assets/faa195a7/yii.activeForm.js"></script>
    <script type="text/javascript">jQuery(document).ready(function () {
            jQuery('#loginform-verifycode-image').yiiCaptcha({"refreshUrl":"\/site\/captcha?refresh=1","hashKey":"yiiCaptcha\/site\/captcha"});
            jQuery('#login-form').yiiActiveForm([{"id":"loginform-username","name":"username","container":".field-loginform-username","input":"#loginform-username","error":".help-block.help-block-error","validate":function (attribute, value, messages, deferred, $form) {yii.validation.required(value, messages, {"message":"用户名不能为空。"});}},{"id":"loginform-password","name":"password","container":".field-loginform-password","input":"#loginform-password","error":".help-block.help-block-error","validate":function (attribute, value, messages, deferred, $form) {yii.validation.required(value, messages, {"message":"密码不能为空。"});}},{"id":"loginform-verifycode","name":"verifyCode","container":".field-loginform-verifycode","input":"#loginform-verifycode","error":".help-block.help-block-error","validate":function (attribute, value, messages, deferred, $form) {yii.validation.required(value, messages, {"message":"验证码不能为空。"});}}], []);
        });</script><script type="text/javascript">
        function formSubmit(){
            var username=$("#loginform-username").val();
            var userpass=$("#loginform-password").val();
            var code=$("#loginform-verifycode").val();
            if(username!=''&&userpass!=''&&code!=''){
                $("#load").show();
            }
        }
    </script>

    <a href="/site/login"><span id="jumpTo" >1</span><span>秒后系统会自动跳转到登录页面，点击本处直接跳</span></a>
</div>

<script>
    countdown(1,"/site/login");
    function countdown(sec,url){
        var i=sec;
        var t=window.setInterval(function(){
            i--;
            if(i===0){
                window.clearInterval(t);
                location.href=url;
            }
            document.getElementById('jumpTo').innerHTML=i;
        },1000);
    }
</script>