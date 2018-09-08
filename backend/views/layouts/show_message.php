<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/10
 * Time: 9:52
 */?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type"
content="text/html; charset=utf-8"  />
<title>跳转提示</title>
<style type="text/css">

*{ padding: 0; margin: 0; }
.system-message{ width:500px;height:100px; margin:auto;border:6px solid #999;text-align:center; position:relative;top:50%;margin-top:200px;left:50%;margin-left:-250px;background-color:#FFF;}
.system-message legend{font-size:24px;font-weight:bold;color:#999;margin:auto;width:100px;}
.system-message h1{ font-size: 100px; font-weight: normal; line-height: 120px; margin-bottom: 12px; }
.system-message .jump{ padding-right:10px;height:25px;line-height:25px;font-size:14px;position:absolute;bottom:0px;left:0px;background-color:#e6e6e1 ; display:block;width:488px;text-align:center;}
.system-message .jump a{ color: #333;}
.system-message .success,.system-message .error{ line-height: 1.8em; font-size: 15px }
.system-message .detail{ font-size: 12px; line-height: 20px; margin-top: 12px; display:none}
</style>
</head>
<body>
<fieldset class="system-message">
    <legend><?php echo $title;?></legend>
    <div style="text-align:center;padding-left:10px;height:75px;width:490px;  ">
        <?php if($type==1):?>
            <p class="success"><?php echo($msg); ?></p>
        <?php else:?>
            <p class="error"><?php echo($msg); ?></p>
        <?php endif;?>
    <p class="detail"></p>
</div>
<p class="jump">页面自动 <a id="href" href="<?php echo($jumpurl); ?>">跳转</a> 等待时间： <b id="wait"><?php echo($wait); ?></b></p>
</fieldset>
<script type="text/javascript">
    (function(){
        var wait = document.getElementById('wait'),href = document.getElementById('href').href;
        totaltime=parseInt(wait.innerHTML);
        var interval = setInterval(function(){
            var time = --totaltime;
            wait.innerHTML=""+time;
            if(time === 0) {
                location.href = href;
                clearInterval(interval);
            };
            }, 1000);
    })();
</script>
</body>
</html>