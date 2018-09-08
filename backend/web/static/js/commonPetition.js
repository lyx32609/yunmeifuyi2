$(function(){
    //缩放图片
    $(".queueList").on('click','img',function(){
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

//附件下载
    $(".queueList_file").on('click','ul li',function(){
        if($(".cover .fileLink").length<=1){
            alert("请上传文件后下载预览");
        }
    });
//弹窗-添加审批人
    $("#approver").on('click',function(){
        $("#sel_Approver").css('display','block');
        $('body').css("overflow","hidden");
        $('body').css("paddingRight","16px"); /* 防止显示滚动条页面跳动 */
        Init($("#company"),"-请选择公司-");
        Init($("#department"),"-请选择部门-");
        $("#staff").html("<li>-请选择人员-</li>");
        $("#staff").prev().val('-请选择人员-');
        //$(".bottom_app textarea").val('');
        $(".bottom_app ul").empty();
        $(".bottom_app").addClass("none");
        arr_copy=[];
        pet_one();
    });

//隐藏选择人员下拉列表
    $("#company,#department").on('click',function(){
        $("#staff").css('display','none');
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
        $("#staff").html("<li>-请选择人员-</li>");
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
        person($(".selBox_staff ul"));
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
                $("#staff").empty();
                $("#staff").append(staff_cont);
            }
        });
    }

//人员选择显示
    function person(obj){
        if(obj.children('li').length==1){
            obj.height('24px');
            obj.css('overflowY','hidden');
        }else{
            if(obj.height()<180){
                obj.height('auto');
            }else{
                obj.css('overflowY','scroll');
                obj.height("180px");
            }
        }
        obj.css('display','block');
    }
    $(".selBox_staff").on('click','input',function(){
        person($(this).next());
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
    $("#staff").mouseenter(function (){
        $(this).show();
    }).mouseleave(function (){
        if($(this).height()>28){
            var selectAll = [];
            var new_arr2=[];
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
                new_arr2[j] = selectAll[j].name;
            }
            if(new_arr2.length==0){
                //$(this).prev().val("-请选择人员-");
            }else{
                //$(this).prev().val(new_arr2);
                var $li_cont = '';
                for(var f=0;f<selectAll.length;f++){
                    $li_cont+="<li class='item'><span>审批人：</span><input type='hidden' value="+selectAll[f].id+"><label style='width:60%'>"+selectAll[f].name+"</label><button id='removed'>移除</button><em class='order_up'></em><i class='order_down'></i></li>";
                }
                $(".bottom_app ul").empty();
                $(".bottom_app ul").append($li_cont);
                if($(".bottom_app ul li").length>0){
                    $(".bottom_app").removeClass("none");
                }else{
                    $(".bottom_app").addClass("none");
                }

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


//签呈流转顺序-移除
    $(".bottom_app ul").on('click','#removed',function(){
        $(this).parent().remove();
        var $bottom=$(this).parent().children('input').val();
        $("#staff li").each(function(index){
            if($(this).children("input").val()==$bottom){
                $(this).removeClass('current');
                $(this).children('input').attr('checked', false);
            }
        });
        if($(".bottom_app ul li").length>0){
            $(".bottom_app").removeClass("none");
        }else{
            $(".bottom_app").addClass("none");
        }
    });
//签呈流转顺序-移动
    $(".bottom_app ul").on('click', '.order_down', function(){
        //判断是否有下一个节点
        if($(this).parents('.item').nextAll().length > 0){
            $(this).parents('.item').next().after($(this).parents('.item').prop('outerHTML'));
            $(this).parents('.item').remove();
        }
    }).on('click', '.order_up', function(){
        //判断是否有上一个节点
        if($(this).parents('.item').prevAll().length > 0){
            $(this).parents('.item').prev().before($(this).parents('.item').prop('outerHTML'));
            $(this).parents('.item').remove();
        }
    });

//确认添加审批人
    var new_arr=[];
    var delete_staff=[];
    var arr_copy=[];
    $("#send_app").on('click',function(){
        $(".bottom_app ul li").each(function(){
            var obj2={};
            obj2.id=$(this).children('input').val();
            obj2.name=$(this).children('label').text();
            arr_copy.push(obj2);
        });
        console.log(arr_copy);
        $("#sel_Approver").css('display','none');
        //关闭弹窗防止屏幕跳动
        $('body').css("overflow","auto");
        $('body').css("paddingRight","0px");

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
            finally_staff+="<li><input type='hidden' value="+arr_finally[k].id+" name='ids[]'><label>"+arr_finally[k].name+"</label><em></em></li>";
        }
        $("ul#copy_staff").empty();
        $("ul#copy_staff").append(finally_staff);
        delete_staff=arr_finally;

        //验证添加审批人
        if(arr_finally.length!=0){
            $("#hidden_box").css('display','none');
            $("#hidden_box").next('em').css('display','none');
            $("ul#copy_staff").css('display','inline-block');
        }
        //列表还原
        $(".selBox_staff ul").css('display','none');
    });

//删除所选审批人
    $("ul#copy_staff").on('click',' li>em',function(){
        $(this).parent().remove();
        var $id = $(this).siblings('input').val();
        //遍历对象数组根据id删除对象
        delete_staff.forEach(function(obj,r){
            if(obj.id == $id){delete_staff.splice(r,1)}
            //console.log(obj,r);
        });
        new_arr=delete_staff;  //删除审批人后，数组对象重新赋值添加到页面

        //验证添加审批人
        if(new_arr.length==0){
            $("#hidden_box").css('display','inline-block');
            $("#hidden_box").next('em').css('display','inline-block');
            $("ul#copy_staff").css('display','none');
        }

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

})