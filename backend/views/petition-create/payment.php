<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Petition */
$type = 3;
$this->title = '签呈发布';
$this->params['breadcrumbs'][] = ['label' => '签呈管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<!--<meta http-equiv="Content-Type" content="text/html; charset=GBK">-->
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
            <div class="cont_tit" style="width:100%;"><h5>付款签呈</h5></div>
            <input type="hidden" name="type" value="3">
            <input name="_csrf-backend" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken?>">
            <dl>
                <dd><label for="purpose"><span>*</span>用途：</label><textarea id="purpose" name="message[purpose]"></textarea></dd>
                <dd>
                    <label for=""><span>*</span>付款方式：</label>
                    <span>
                        <input type="radio" checked="checked" name="message[mode]" value="1"><label for="">现金</label>
                        <input type="radio" name="message[mode]" value="2"><label for="">转账</label>
                        <input type="radio" name="message[mode]" value="3"><label for="">其他</label>
                    </span>
                </dd>
                <dd><label for="name2"><span>*</span>收款人姓名：</label><input id="name2" type="text" name="message[name]"></dd>
                <dd><label for="account"><span>*</span>收款账号：</label><input id="account" type="text" name="message[account]"></dd>
                <dd><label for="accountbank"><span>*</span>开户银行：</label><input id="accountbank" type="text" name="message[accountbank]"></dd>
                <dd><label for="money"><span>*</span>金额：</label><input id="money" type="text" name="message[money]"></dd>

                <dd class="position_r" style="clear: left;">
                    <label for="" style="display: block;" class="float_f"><span>&nbsp;&nbsp;</span>图片：</label>
                    <div id="uploader" class="uploaderAll">
                        <div class="queueList">
                            <div  class="placeholder">
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
                        <div class="cover">
                            <!--<iframe class="fileSrc" src=""  width="100%" height="100%" style="height:800px;">-->
                                <!--<a class="fileLink" href="">Download PDF</a>-->
                            <!--</iframe>-->
                            <!--<em>关闭</em>-->
                        </div>
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
    //附件预览
//    $(".queueList_file").on('click','ul li',function(){
//        var num=$(this).index();
//        if($(".cover .fileLink").length>0){
//            $(".cover").css('display','block');
//            $(".cover .fileLink").eq(num).attr("target","_blank").siblings().removeAttr("target");
//        }else{
//            alert("请上传文件后预览");
//        }
//    });
//    $(".cover,.cover em").on('click',function(){
//        $(".cover").css('display','none');
//    });

    $(function(){
        $("#signForm").validate({
            errorElement: "em",
            rules:{
                //付款签呈
                "message[purpose]":"required",
                "message[name]":"required",
                "message[account]":{
                    "required":true,
                    "number":true
                },
                "message[accountbank]":"required",
                "message[money]":{
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
</script>
<script src="/static/js/commonPetition.js"></script>

