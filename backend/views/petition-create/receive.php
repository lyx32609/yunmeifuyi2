<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Petition */
$type = 1;
$this->title = '签呈发布';
$this->params['breadcrumbs'][] = ['label' => '签呈管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<link href="/static/css/petition.css" type="text/css" rel="stylesheet"/>
<link href="/static/css/webuploader/webuploader.css" type="text/css" rel="stylesheet"/>
<link href="/static/css/webuploader/demo.css" type="text/css" rel="stylesheet"/>
<script src="/static/js/webuploader/webuploader.js"></script>
<script src="/static/js/webuploader/imgUp.js"></script>
<script src="/static/js/webuploader/fileUp.js"></script>
<!--<script src="/static/js/webuploader/Uploader.swf"></script>-->
<style>
    span.multiselect-native-select{
        float: left;
        width:150px;
        margin:10px 6px 0 6px;
    }
</style>
<div class="petition-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <form action="/petition/create" id="signForm" enctype="multipart/form-data" method="post">
        <?php include("head.php")?>
        <hr style="border:1px solid #ccc;margin-top:0;"/>
        <div class="cont">
            <div class="cont_tit" style="width:100%;"><h5>领用签呈</h5></div>
            <input type="hidden" name="type" value="1">
            <input name="_csrf-backend" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken?>">
            <dl>
                <dd><label for="reason"><span>*</span>原因：</label><textarea id="reason" name="message[reason]"></textarea></dd>
                <dd>
                    <label for="name"><span>*</span>物品：</label>
                    <div class="more">
                        <input type="button" id="messages" value="添加">
                        <div class="more_cont">
                            <div class="need"><label for="name"><span style='margin: 0;'>*</span>名称：</label><input id="name" type="text" name="message[message][0][name]"></div>
                            <div class="need ml_20"><label for="num"><span style='margin: 0;'>*</span>数量：</label><input id="num" type="text" name="message[message][0][num]"></div>
                        </div>
                    </div>
                </dd>

                <dd style="clear: left;">
                    <label for="" style="display: block;" class="float_f"><span>&nbsp;&nbsp;</span>图片：</label>
                    <div id="uploader" class="uploaderAll">
                        <div class="queueList">
                            <div  class="placeholder">
                                <div id="img_Picker" style="display: none"></div>
                                <div id="imgPicker"></div>
                            </div>
                        </div>
                        <div class="statusBar" style="display:none;">
                            <div class="progress">
                                <span class="text">0%</span>
                                <span class="percentage"></span>
                            </div>
                            <div class="btns">
                                <div id="imgPicker2"></div>
                                <div class="uploadBtn">开始上传</div>
                            </div>
                        </div>
                        <div id="img_copy"></div>
                        <input type="hidden" id="imgUrl" name="master_img">
                    </div>
                </dd>

                <dd style="clear: left;">
                    <label for="" class="float_f"><span>&nbsp;&nbsp;</span>附件：</label>
                    <div id="uploader_file" class="uploaderAll">
                        <div class="queueList queueList_file">
                            <div  class="placeholder">
                                <div id="filePicker"></div>
                            </div>
                        </div>
                        <div class="statusBar" style="display:none;">
                            <div class="progress">
                                <span class="text">0%</span>
                                <span class="percentage"></span>
                            </div>
                            <div class="btns">
                                <div id="filePicker2"></div>
                                <div class="uploadBtn">开始上传</div>
                            </div>
                        </div>
                        <input type="hidden" id="fileUrl" name="file">
                        <div class="cover_copy">
                            <ul></ul>
                        </div>
                        <div class="cover"></div>
                    </div>
                </dd>
                <dd class="position_em">
                    <label for="" class="float_f"><span>*</span>审批人：</label>
                    <div class="position_box">
                        <input type="text" id="hidden_box" name="approver_id" readonly>
                        <ul id="copy_staff"></ul>
                        <input type="button" id="approver" value="添加">
                        <div id="sel_Approver">
                            <div class="selBox">
                                <div class="selBox_tit"><h5>审批人</h5><span></span></div>
                                <div class="selBox_cont">
                                    <div class="select_box">
                                        <select name="" id="company">
                                            <option>-请选择公司-</option>
                                        </select>
                                    </div>
                                    <div class="select_box">
                                        <select name="" id="department">
                                            <option>-请选择部门-</option>
                                        </select>
                                    </div>
                                    <div class="selBox_staff">
                                        <span></span>
                                        <input type="text" value="-请选择人员-" readonly>
                                        <ul id="staff"></ul>
                                    </div>
                                </div>
                                <div class="bottom " style="clear: both;width:100%;">
                                    <div class="bottom_app none">
                                        <h5 style="width:100%;text-align: center">签呈流转顺序</h5>
                                        <!--<textarea></textarea>-->
                                        <ul style="border:1px solid #ccc;padding-left:0;"></ul>
                                    </div>
                                    <input type="button" id="send_app" value="确认" style="width:100px;margin:30px auto 20px auto;">
                                </div>
                            </div>
                        </div>
                    </div>
                </dd>
            </dl>
        </div>
        <div id="send"><input type="submit" value="发布"></div>
</div>

</form>
</div>
<script>
    $(function(){
        $("#signForm").validate({
            errorElement: "em",
            rules:{
                "message[reason]":"required",
                "message[message][0][name]":"required",
                "message[message][0][num]":{
                    "required":true,
                    "number":true
                },
                approver_id:{
                    "required":true,
                },
            },
            messages:{
                approver_id:{
                    "required":"请添加审批人",
                }
            },
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            },
        });
        $.extend($.validator.messages, {
            "required": "必填信息",
            "number": "请输入有效的数字",
        });

    });

    //添加多个
    $("#messages").on('click',function(){
        var cont_mess='';
            for(var k=0;k<$(".more_cont").length;k++){
                cont_mess="<div class='more_cont'>" +
                    "<div class='need'><label class='ml_6'>名称：</label><input type='text' name='message[message]["+(k+1)+"][name]'></div>" +
                    "<div class='need ml_20'><label class='ml_6'>数量：</label><input type='text' name='message[message]["+(k+1)+"][num]'></div>" +
                    "</div>";
            }
        $(".more").append(cont_mess);
    });
</script>
<script src="/static/js/commonPetition.js"></script>

