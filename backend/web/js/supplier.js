$(function(){                
         
        //以下代码为了兼容iOS、Android
        var bind_name = 'input';
        if (navigator.userAgent.indexOf("MSIE") != -1) {    //（此处是为了兼容IE）
         bind_name = 'propertychange';
        }
        if(navigator.userAgent.match(/android/i) == "android")
        {
         bind_name = "keyup";
        }
        //数量
        $(".num_only").bind(bind_name,function(){
          this.value=this.value.replace(/[^\d]/g,'');
        });
        //执照编码只能12位或15位数字或18位数字或字母组合---执照编号
        $(".num_alph").bind(bind_name,function(){          
          var $length=this.value.length;
              if($length==18){             
                this.value=this.value.replace(!/^(\d{17})([0-9]|[A-Z])$/g,'');
              }else{
                this.value=this.value.replace(/[^\d]/g,'');
              }
        });
        $("#license_number").blur(function(){
            var $length=this.value.length;
                if($length==12 || $length==15){
                   this.value=this.value.replace(/[^\d]/g,'');
                }               
                else if($length==18){
                   this.value=this.value.replace(!/^(\d{17})([0-9]|^[A-Z])$/,'');
                }
                else{
                  this.value="";
                }
        })
        //金额、面积浮点数
        $(".num_float").bind(bind_name,function(){
          clearNoNum(this);
        });

        //只保留两位小数
        function clearNoNum(obj){
          obj.value = obj.value.replace(/[^\d.]/g,""); //清除"数字"和"."以外的字符
          obj.value = obj.value.replace(/^\./g,""); //验证第一个字符是数字而不是
          obj.value = obj.value.replace(/\.{2,}/g,"."); //只保留第一个. 清除多余的
          obj.value = obj.value.replace(".","$#$").replace(/\./g,"").replace("$#$",".");
          obj.value = obj.value.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3'); //只能输入两个小数
        }
        //获取字符串的字节长度
        function len(s) {
           s = String(s);
           return s.length + (s.match(/[^\x00-\xff]/g) || "").length;// 加上匹配到的全角字符长度
        }
        function limit(obj, limit) {
           var val = obj.value;
               if (len(val) > limit) {
               val=val.substring(0,limit);
               while (len(val) > limit){
                  val = val.substring(0, val.length - 1);
                };
               obj.value = val;
              }
        }

        $(".limit_18").keyup(function(){
            limit(this,18);//18字节内
        });
        $(".limit_100").keyup(function(){
            limit(this,100);//100字节内
        });
        $(".limit_200").bind(bind_name,function(){
            var limitSub=$(this).val().substr(0,200);
            $(this).val(limitSub);  //截取字符长度
            $(this).next('.statistics').html(limitSub.length+'/200'); //获取实时输入字符长度
            if(limitSub.length==200){
                 $('.limit').css('color','red');
             }else{
                 $('.limit').css('color','#333');
             }
        });

        //添加配送车辆类型和数量
          $(".carmessage i").on('click',function(){
            var $length=$(".addmessage").length;
             for(var i=0;i<$length;i++){                
               var $addmessage="<div class='addmessage'><span>配送车辆</span> <input class='w_5' name='distribution_car["+$length+"][type]' type='text' placeholder='请输入车辆类型' notNull='车辆类型'> <span>数量</span> <input  class='w_3 num_only' name='distribution_car["+$length+"][num]' type='text' placeholder='请输入数字' notNull='车辆数量'></div>";                
             }  
             $(".carmessage").append($addmessage);
             $(".num_only").bind(bind_name,function(){
                this.value=this.value.replace(/[^\d]/g,'');
              });
                 //console.log($length);                                           
          });  
         //验证不为空
        $(".send").click(function(){        	
            var $license_number=$("#license_number").val();//执照编号
            var $registered_capital=$("#registered_capital").val(); //注册资金
            var $operating_area=$("#operating_area").val();//经营面积
            var $business_address=$("#business_address").val();//经营地址
            var $headcount=$("#headcount").val();//人员数量
            var $agent_brand=$("#agent_brand").val();//代理品牌
            var $agent_num=$("#agent_num").val();//几级代理
            var $agencyarea=$("#agencyarea").val();//代理区域
            var $product_area=$("#product_area").val();//销售区域
            var $delivery_car=$(".delivery_car").val();//配送车辆
            var $delivery_num=$(".delivery_num").val();//配送车辆数量
            var $delregion=$("#delregion").val();//服务区域
            var $delivery_comm=$("#delivery_comm").val();//配送商户
            var $delivery_comy=$("#delivery_comy").val();//配送商品
            var $product_count=$("#product_count").val();//生产人员
            var $product_brand=$("#product_brand").val();  //(生产)产品品牌                      
            var $delivery_person=$("#delivery_person").val();//配送人员

                                                
            //执照编号
            if($license_number==''){ 
                notNull($("#license_number"));                 
                return false;
            }
            //注册资金
            if($registered_capital==''){
            	  notNull($("#registered_capital"));                 
                return false;
            }
            //经营面积
            if($operating_area==''){
                notNull($("#operating_area"));                 
                return false;
            }
            //经营地址
            if($business_address==''){
            	  notNull($("#business_address"));                  
                return false;
            }
            //人员数量
            if($headcount==''){
               notNull($("#headcount"));              
               return false;
            }
            //代理品牌
            if($agent_brand==''){
               notNull($("#agent_brand"));               
               return false;
            }
            //几级代理
            if($agent_num==''){
               notNull($("#agent_num"));                 
               return false;
            }
            //代理区域
            if($agencyarea==''){
               notNull($("#agencyarea"));                  
               return false;
            }
             //生产商--生产人员
            if($product_count==''){
               notNull($("#product_count"));                 
               return false;
            }
             //生产商--产品品牌
            if($product_brand==''){
               notNull($("#product_brand"));               
               return false;
            }

            //生产商--销售区域
            if($product_area==''){
               notNull($("#product_area"));                 
               return false;
            }
            //供货商---配送车辆
             if($delivery_car==''){
               notNull($(".delivery_car"));                 
               return false;
            }
            //配送车辆数量
             if($delivery_num==''){
               notNull($(".delivery_num"));                 
               return false;
            }
          
            //供货商---服务区域
            if($delregion==''){
               notNull($("#delregion"));                 
               return false;
            }
            //配送商户
             if($delivery_comm==''){
               notNull($("#delivery_comm"));                 
               return false;
            }
            //生产商---配送人员
             if($delivery_person==''){
               notNull($("#delivery_person"));                
               return false;
            }
            //配送商品
             if($delivery_comy==''){
               notNull($("#delivery_comy"));                 
               return false;
            }                                  
        })        
 });
function notNull(obj){
    $(".btn p").fadeIn(1000);
    $(".btn p").fadeOut(1000);
    $(".btn p").html(obj.attr('notNull')+"不能为空");
    obj.focus();
}