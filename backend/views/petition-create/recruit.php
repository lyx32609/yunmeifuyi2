<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Petition */
$type = 14;
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
            <div class="cont_tit" style="width:100%;"><h5>招聘签呈</h5></div>
            <input type="hidden" name="type" value="14">
            <input name="_csrf-backend" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken?>" >
            <dl>
                <dd><label for="demanddepartment"><span>*</span>需求部门：</label><input type="text" id="demanddepartment" name="message[demanddepartment]"></dd>
                <dd><label for="applytime"><span>*</span>申请时间：</label><input type="text" name="message[applytime]" id="applytime"></dd>
                <dd><label for="stationtime"><span>*</span>人员到岗时间：</label><input id="stationtime" type="text" name="message[stationtime]"></dd>
                <dd><label for="dutyname"><span>*</span>需求岗位或职务名称：</label><input type="text" id="dutyname" name="message[dutyname]"></dd>
                <dd><label for="dutynum"><span>*</span>需求名额：</label><input type="text" id="dutynum" name="message[dutynum]"></dd>
                <dd><label for="plandutyname"><span>*</span>拟定试用期岗位名称：</label><input type="text" id="plandutyname" name="message[plandutyname]"></dd>
                <dd><label for=""><span>*</span>拟定待遇</dd>
                <dd><label for="planprobation"><span>*</span>拟定试用期：</label><input type="text" id="planprobation" name="message[planprobation]"></dd>
                <dd><label for="planregularworker"><span>*</span>拟定转正后：</label><input type="text" id="planregularworker" name="message[planregularworker]"></dd>
                <dd>
                    <label for=""><span>*</span>人员需求说明：</label>
                    <span id="isCheckbox_explain">
                        <input type="checkbox" name="message[dutyreasonexplain][0]" value="0"><label for="">扩大编制</label>
                        <input type="checkbox" name="message[dutyreasonexplain][1]" value="1"><label for="">业务扩充</label>
                        <input type="checkbox" name="message[dutyreasonexplain][2]" value="2"><label for="">补充离职或调动</label>
                        <input type="checkbox" name="message[dutyreasonexplain][3]" value="3"><label for="">储备人力</label>
                        <input type="checkbox" name="message[dutyreasonexplain][4]" value="4"><label for="">短期需要</label>
                    </span>
                </dd>
                <!--<dd><label for="detailexplain"><span>*</span>详细说明：</label><textarea id="detailexplain" name="message[detailexplain]"></textarea></dd>-->
                <dd><label for="" style="display: block;" class="float_f"><span>&nbsp;&nbsp;</span>详细说明：</label><textarea name="message[detailexplain]"></textarea></dd>
                <dd>
                    <label><span>*</span>性别：</label>
                    <span>
                        <input type="radio" checked="checked" name="message[sex]" value="0"><label for="">男</label>
                        <input type="radio" name="message[sex]" value="1"><label for="">女</label>
                        <input type="radio" name="message[sex]" value="2"><label for="">不限</label>
                    </span>
                </dd>
                <dd><label for="age"><span>*</span>年龄：</label><input type="text" id="age" name="message[age]"></dd>
                <dd>
                    <label><span>*</span>教育程度：</label>
                    <span>
                        <input type="radio" name="message[education]" value="0"><label for="">中专</label>
                        <input type="radio" name="message[education]" value="1"><label for="">大专</label>
                        <input type="radio" checked="checked" name="message[education]" value="2"><label for="">本科</label>
                        <input type="radio" name="message[education]" value="3"><label for="">硕士</label>
                        <input type="radio" name="message[education]" value="4"><label for="">博士</label>
                        <input type="radio" name="message[education]" value="5"><label for="">其他</label>
                    </span>
                </dd>
                <dd>
                    <label for=""><span>*</span>来源：</label>
                    <span id="isCheckbox_origin">
                        <input type="checkbox" name="message[adjust][0]" value="0"><label for="">内调</label>
                        <input type="checkbox" name="message[adjust][1]" value="1"><label for="">外招</label>
                    </span>
                </dd>
                <dd><label for="major"><span>*</span>专业：</label><input type="text" id="major" name="message[major]"></dd>
                <dd><label for="experience"><span>*</span>经验：</label><input type="text" id="experience" name="message[experience]"></dd>
                <dd><label for="experienceyear"><span>*</span>经验年限：</label><input type="text" id="experienceyear" name="message[experienceyear]"></dd>
                <dd><label for="stationresponsibility"><span>*</span>岗位职责：</label><textarea id="stationresponsibility" name="message[stationresponsibility]"></textarea></dd>
                <dd><label for="officerequire"><span>*</span>任职需求：</label><textarea id="officerequire" name="message[officerequire]"></textarea></dd>
                <!--<dd><label for="elserequire"><span>*</span>其他要求：</label><textarea id="elserequire" name="message[elserequire]"></textarea></dd>-->
                <dd><label for="" style="display: block;" class="float_f"><span>&nbsp;&nbsp;</span>其他要求：</label><textarea  name="message[elserequire]"></textarea></dd>

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
<script>
    $(function(){
        $("#applytime").datepicker();
        $("#stationtime").datepicker();
        $("#applytime").change(dateChange);
        $("#stationtime").change(dateChange);
        function dateChange(){
            if($(this).val()!=''){
                $(this).next().css('display','none');
            }else{
                $(this).next().css('display','block');
            }
        }
        $("#signForm").validate({
            errorElement: "em",
            rules:{
                //招聘签呈
                "message[demanddepartment]":"required",//需求部门
                "message[applytime]": {
                    "required": true,
                    "dateISO": true
                },
                "message[stationtime]": {
                    "required": true,
                    "dateISO": true
                },
                "message[dutyname]":"required",
                "message[dutynum]":{//需求名额
                    "required":true,
                    "number":true
                },
                "message[plandutyname]":"required",//拟定试用期岗位名称

                "message[planprobation]":{//拟定试用期
                    "required":true,
                    "number":true
                },
                "message[planregularworker]":{//拟定转正后
                    "required":true,
                    "number":true
                },
                "message[age]":{//年龄
                    "required":true,
                    "number":true
                },
                "message[major]":"required",//专业
                "message[experience]":"required",//经验
                "message[experienceyear]":{//经验年限
                    "required":true,
                    "number":true
                },
                "message[stationresponsibility]":"required",//岗位职责
                "message[officerequire]":"required",//任职需求
          
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
            submitHandler:function(form){
                if($("#isCheckbox_explain input").is(":checked") && $("#isCheckbox_origin input").is(":checked")){
                    form.submit();
                }else{
                    if($("#isCheckbox_explain input").is(":checked")==false){
                        alert("请选择人员需求说明");
                    }else if($("#isCheckbox_origin input").is(":checked")==false){
                        alert("请选择来源");
                    }
                }
            }

        });
        $.extend($.validator.messages, {
            "required": "必填信息",
            "number": "请输入有效的数字",
            "dateISO": "请输入有效的日期 (YYYY/MM/DD)",
        });

    });

    $(this).on('click',function(){
        $(this).prop("checked","checked");
        if($(this).is(":checked")){
            $(this).prop("checked",false);
            return;
        }else{
            $(this).prop("checked",true);
            return;
        }
    });
</script>
<script src="/static/js/commonPetition.js"></script>
