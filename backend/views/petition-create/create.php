<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Petition */
$type = 3;
$this->title = '签呈发布';
$this->params['breadcrumbs'][] = ['label' => '签呈管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<link href="/static/css/petition.css" type="text/css" rel="stylesheet"/>
<style>
    span.multiselect-native-select{
        float: left;
        width:150px;
        margin:10px 6px 0 6px;
    }
</style>
<div class="petition-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <form action="" id="signForm" enctype="multipart/form-data">
        <?php include("head.php")?>
        <hr style="border:1px solid #ccc;margin-top:0;"/>
        <div class="cont">
            <div class="cont_tit" style="width:100%;"><h5>付款签呈</h5></div>
            <dl>
                <dd><label for=""><span>*</span>用途：</label><input type="text" name="purpose"></dd>
                <dd>
                    <label for=""><span>*</span>付款方式：</label>
                    <span>
                        <input type="radio" checked="checked" name="money"><label for="">现金</label>
                        <input type="radio" name="money"><label for="">转账</label>
                        <input type="radio" name="money"><label for="">其他</label>
                    </span>
                </dd>
                <dd><label for=""><span>*</span>收款人姓名：</label><input type="text" name="fullName"></dd>
                <dd><label for=""><span>*</span>收款账号：</label><input type="text" name="account_number"></dd>
                <dd><label for=""><span>*</span>开户银行：</label><input type="text" name="bank"></dd>
                <dd><label for=""><span>*</span>金额：</label><input type="text" name="amount_money"></dd>
                <dd class="position_r" style="clear: left;">
                    <label for="" style="display: block;float: left;"><span>&nbsp;&nbsp;</span>图片：</label>
                    <div id="img_list" class="file_list"></div>
                    <label class="position_a" for="CoverPicUrl">添加</label>
                    <input id="CoverPicUrl" style="display: none;" type="file" value="" accept = "image/png, image/jpeg, image/gif, image/jpg"  multiple>
                    <div id="img_copy"></div>
                </dd>
                <dd class="position_r" style="clear: left;padding:15px 0 0 0;">
                    <label for="files_list"><span>&nbsp;&nbsp;</span>附件：</label>
                    <div id="files_list" class="file_list"></div>
                    <label class="position_a" for="filesMult">添加</label><input id="filesMult" style="display: none;" type="file" value="" multiple>
                </dd>
                <dd class="position_r position_em" style="clear: left;">
                    <label for="" style="display: block;float: left;"><span>*</span>审批人：</label>
                    <input type="text" id="hidden_box" name="hidden_box" value=""  readonly>
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
                            <div class="bottom" style="clear: both;width:100%;">
                                <div class="bottom_app">
                                    <span style="font-weight: normal">审批人：</span>
                                    <textarea></textarea>
                                </div>
                                <input type="button" id="send_app" value="确认" style="width:100px;margin:10px auto 10px auto;">
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
        validate();
    });
    function validate(){
        $("#signForm").validate({
            errorElement: "em",
            rules:{
                //付款签呈
                purpose:"required",
                fullName:"required",
                account_number:{
                    "required":true,
                    "number":true
                },
                bank:"required",
                amount_money:{
                    "required":true,
                    "number":true
                },
                hidden_box:{
                    "required":true,
                },
            },
            messages:{
                hidden_box:{
                    "required":"请添加审批人",
                }
            },
            errorPlacement: function(error, element) {
                error.insertAfter(element);
            }
        });
        $.extend($.validator.messages, {
            "required": "必填信息",
            "number": "请输入有效的数字",
        });
    }



    //html5实现图片预览功能
    $("#CoverPicUrl").change(function (e) {
        console.log(e.target.files);
        var CoverPicUrl = e.target.files;
        for(var i=0;i<15;i++){
            //console.log(CoverPicUrl.length);
            if (CoverPicUrl[i]) {
                if(CoverPicUrl.length<=15){
                    $("#img_list").empty();
                    var reader = new FileReader();
                    var type=$("#CoverPicUrl").val().substring($("#CoverPicUrl").val().lastIndexOf(".") + 1).toLowerCase();
                    //console.log(type);
                    if(type!=="png" && type!=="jpeg" && type!=="gif" && type!=="jpg"){
                        alert("上传图片格式不正确，请重新上传");
                        return false;
                    }else{
                        reader.onload = function () {
                            var imgList="<div id='imgBox'><img src="+this.result+" alt=''/><em></em></div>";
                            $("#img_list").append(imgList);

                        }
                        reader.readAsDataURL(CoverPicUrl[i]);
                    }

                }else{
                    alert("最多只能选15张图片");
                    return false;
                }
            }else{
                return false;
            }
        }

    });

    //放大图片
    $("#img_list").on('click','img',function(){
        console.log($(this));
        $("#img_copy").addClass("coverBox");
        $("#img_copy").append($(this).addClass("active").clone());
        $('body').css("overflow","hidden");
        $('body').css("paddingRight","16px"); /* 防止显示滚动条页面跳动 */
    });
    $("#img_copy").on('click',function(){
        $(this).removeClass("coverBox");
        $(this).empty();
        $('body').css("overflow","auto");
        $('body').css("paddingRight","0px");
    });

    //删除图片
    $("#img_list").on('click','em',function () {
        $(this).parent().remove();
    })

    //弹窗-添加审批人
    $("#approver").on('click',function(){
        $("#sel_Approver").css('display','block');
        $('body').css("overflow","hidden");
        $('body').css("paddingRight","16px"); /* 防止显示滚动条页面跳动 */
        Init($("#company"),"-请选择公司-");
        Init($("#department"),"-请选择部门-");
        $("#staff").html("<li>-请选择人员-</li>");
        $("#staff").prev().val('-请选择人员-');
        $(".bottom_app textarea").val('');
        pet_one();
    });



    //获取公司
    function pet_one(){
        $.get('/petition/get-company',function(res){
               var arr_company=JSON.parse(res);
               //console.log(arr_company);
               var company_cont='';
               for(var i=0;i<arr_company.length;i++){
                   company_cont+="<option value="+arr_company[i].id+">"+arr_company[i].name+"</option>";
               }
               $("#company").append(company_cont);
           });
    }
    $("#company").change(function(){
        //console.log( $("#company option:selected").text());
        var companyPet_id= parseInt($("#company option:selected").val());
            Init($("#department"),"-请选择部门-");
            $("#staff").html("<li'>-请选择人员-</li>");
            $("#staff").prev().val('-请选择人员-');
            if($("#company option:selected").text()!="-请选择公司-"){
                pet_two(companyPet_id);
            }else{
                return;
            }

    });


    //获取部门
    function pet_two(companyPet_id){
        //console.log(companyPet_id);
        $.get('/petition/get-department?id='+companyPet_id+'',function(res){
            var arr_department=JSON.parse(res);
            //console.log(arr_department);
            var department_cont='';
            for(var i=0;i<arr_department.length;i++){
                department_cont+="<option value="+arr_department[i].id+">"+arr_department[i].name+"</option>";
            }
            $("#department").append(department_cont);

        });

    }
    $("#department").change(function(){
        var companyPet_id= parseInt($("#company option:selected").val());
        var departmentPet_id= parseInt($("#department option:selected").val());
            $("#staff").html("<li>-请选择人员-</li>");
            $("#staff").prev().val('-请选择人员-');
        if($("#department option:selected").text()!="-请选择部门-"){
            pet_three(companyPet_id,departmentPet_id);
        }else{
            return;
        }
    });

    //获取人员
    function pet_three(companyPet_id,departmentPet_id){
        //console.log(companyPet_id);
        $.get('/petition/get-user?id='+departmentPet_id+'&company='+companyPet_id+'',function(res){
            var arr_staff=JSON.parse(res);
            //console.log(arr_staff);
            if(arr_staff=="该部门暂时没有人员"){
                alert("该部门暂时没有人员");
            }else{
                var staff_cont='';
                for(var i=0;i<arr_staff.length;i++){
                    staff_cont+="<li id="+i+"><input type='checkbox' value="+arr_staff[i].id+"><label>"+arr_staff[i].name+"</label></li>";
                }
                $("#staff").append(staff_cont);
            }

        });
    }

    //人员选择显示
    $(".selBox_staff").on('click','input',function(){
        //console.log($(this).next());
        if($(this).next().children('li').length==0){
            $(this).next().css('height','30px');
            $(this).next().css('display','block');
        }else{
            var num=$(this).next().children('li').length+1;
            $(this).next().css('height',num*30);
            //console.log($(this).next().height());
            $(this).next().css('display','block');
            if($(this).next().height()>180){
                $(this).next().height('180px');
                $(this).next().css('overflowY','scroll');
            }
        }
    });
    //选择多个人员
    $("#staff").on('click','li',function(){
        if($(this).hasClass('current')){
            $(this).removeClass('current');
            $(this).children('input').attr('checked',false);
        }else{
            $(this).addClass('current');
            $(this).children('input').attr('checked',true);
        }
    });
    //显示所选审批人
    var arr_copy=[];
    $("#staff").mouseover(function (){
        $(this).show();
    }).mouseout(function (){
        if($(this).height()>28){
            var selectAll = [];
            var new_arr=[];
            $(this).children('li').each(function(){
                if($(this).is('.current')){
                    var obj={};
                        obj.id=$(this).children('input').val();
                        obj.name=$(this).children('label').text();
                        selectAll.push(obj);
                }
            });
            //console.log(selectAll);
            for(var j=0;j<selectAll.length;j++) {
                new_arr[j] = selectAll[j].name;
            }
            //console.log(new_arr);
            if(new_arr.length==0){
                $(this).prev().val("-请选择人员-");
            }else{
                $(this).prev().val(new_arr);
                $(".bottom_app textarea").val(new_arr);
                arr_copy=selectAll;
            }

            $(this).hide();
        }else{
            $(this).hide();
            return;
        }
    });

    //初始化联动
    function Init(node,tips) {
        return node.html("<option>"+tips+"</option>");
    }


    //确认添加审批人
    var new_arr=[];
    var delete_staff=[];
    $("#send_app").on('click',function(){
        //console.log(arr_copy);
        $("#sel_Approver").css('display','none');

        if(new_arr.length==0){
            new_arr=arr_copy;
        }else{
            var temp=[];
                temp=new_arr;
                new_arr=temp.concat(arr_copy);
        }
        //console.log(new_arr);

        //去除重复选择的审批人
        var arr_finally = uniqeByKeys(new_arr,['id']);
            //console.log(arr_finally);

        //添加审批人到页面
        var finally_staff='';
            for(var k=0;k<arr_finally.length;k++){
                finally_staff+="<li><input type='hidden' value="+arr_finally[k].id+"><label>"+arr_finally[k].name+"</label><em></em></li>";
            }
            $("ul#copy_staff").empty();
            $("ul#copy_staff").append(finally_staff);
            delete_staff=arr_finally;



            //确认添加审批人-清除联动
            Init($("#company"),"-请选择公司-");
            Init($("#department"),"-请选择部门-");
            $("#staff").html("<li>-请选择人员-</li>");
            $("#staff").prev().val('-请选择人员-');
            //关闭弹窗防止屏幕跳动
            $('body').css("overflow","auto");
            $('body').css("paddingRight","0px");
            pet_one();



            if($("ul#copy_staff").html()!=""){
                $("#hidden_box").next().css('display','none');
            }else{
                console.log($("ul#copy_staff").html());
            }
    });

    //删除所选审批人
    $("ul#copy_staff").on('click',' li>em',function(){
        $(this).parent().remove();
        var $id=$(this).siblings('input').val();
        //遍历对象数组根据id删除对象
        delete_staff.forEach(function(obj,r){
            if(obj.id==$id){delete_staff.splice(r,1)}
                console.log(obj,r);
        });
        new_arr=delete_staff;  //删除审批人后，数组对象重新赋值添加到页面

        if($("#hidden_box").val($(this).parent().length)==''){
            $("#hidden_box").css('display','inline-block');
            $("#copy_staff").css('display','none');
        }
        validate(); //审批人验证

    });

    //将对象元素转换成字符串以作比较
    function obj2key(obj, keys){
        var n = keys.length,
            key = [];
        while(n--){
            key.push(obj[keys[n]]);
        }
        return key.join('|');
    }
    //去重操作
    function uniqeByKeys(array,keys){
        var arr = [];
        var hash = {};
        for (var i = 0, j = array.length; i < j; i++) {
            var k = obj2key(array[i], keys);
            if (!(k in hash)) {
                hash[k] = true;
                arr.push(array[i]);
            }
        }
        return arr ;
    }

    //关闭弹窗
    $(".selBox_tit span").on('click',function(){
        $("#sel_Approver").css('display','none');
        //关闭弹窗防止屏幕跳动
        $('body').css("overflow","auto");
        $('body').css("paddingRight","0px");
    });
</script>
