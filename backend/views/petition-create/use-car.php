<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Petition */
$type = 2;
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
            <div class="cont_tit" style="width:100%;"><h5>用车签呈</h5></div>
            <input type="hidden" name="type" value="2">
            <input name="_csrf-backend" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken?>">
            <dl>
                <dd><label for="carmodel"><span>*</span>车型：</label><input id="carmodel" type="text" name="message[carmodel]"></dd>
                <dd><label for="purpose"><span>*</span>用途：</label><textarea id="purpose" name="message[purpose]"></textarea></dd>
                <dd><label for="number"><span>*</span>用车数量：</label><input id="number" type="text" name="message[number]"></dd>
                <dd><label for="termini"><span>*</span>目的地：</label><input id="termini" type="text" name="message[termini]"></dd>
                <dd>
                    <label for=""><span>*</span>日期：</label>
                    <div class="date">
                        <div class="date_box">
                            <label for="date_start"></label><input id="date_start" type="text" name="message[date][date_start]">
                        </div>
                        <span style="margin:0 10px;float: left">-</span>
                        <div class="date_box">
                            <label for="date_end"></label><input id="date_end" type="text" name="message[date][date_end]">
                        </div>
                    </div>
                </dd>

                <dd class="position_r" style="clear: left;">
                    <label for="" style="display: block;" class="float_f"><span>&nbsp;&nbsp;</span>图片：</label>
                    <div id="uploader" class="uploaderAll">
                        <div class="queueList">
                            <div  class="placeholder">
                                <div id="imgPicker" style="height:30px;"></div>
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

                <dd class="position_r" style="clear: left;">
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
<script src="/static/jquery-ui/times.js"></script>
<script>
    //验证日期不为空
    $("#date_start").change(dateChange);
    $("#date_end").change(dateChange);
    function dateChange(){
        if($(this).val()!=''){
            $(this).next().css('display','none');
        }else{
            $(this).next().css('display','block');
        }
    }

    $(function(){
        $("#signForm").validate({
            errorElement: "em",
            rules:{
                //通用签呈
                "message[carmodel]":"required",
                "message[purpose]":"required",
                "message[number]":{
                    "required":true,
                    number:true
                },
                "message[termini]":"required",
                "message[date][date_start]":{
                    "required":true,

                },
                "message[date][date_end]":{
                    "required":true,

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
</script>
<script src="/static/js/commonPetition.js"></script>
