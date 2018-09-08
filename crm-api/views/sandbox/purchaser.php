<style>
.wrap > .container {
	font-size: 14px;
}
.wrap > .container label{
	width: 155px;
    height: 30px;
    line-height: 30px;
	
}
.wrap > .container input[type='text']{
	height: 27px;
	width: 200px;
}
.wrap > .container #params select{
	width: 200px;
	height: 24px;
}

</style>
<?php
use app\models\Api;
use yii\helpers\Url;
use yii\web\View;
use benben\widgets\JsonView;
use yii\base\Widget;
use app\models\Order;
use app\benben\assets\JQueryFromAsset;

/* @var $this yii\web\View */

app\benben\JQueryFormAsset::register($this, View::POS_READY);

?>
<div class="wrap">
<div class="container">
<form method="post" action="<?= Url::toRoute('request');?>" id="testForm" autocomplete="off">
    <div>
        <label>APPID:</label>
        <input type="text" name="appid" value="1610001" />
    </div>
    <div>
        <label>SECRET:</label>
        <input type="text" name="secret" value="hh8bf094169a40a3bd188ba37ebe872v" />
    </div>
    <div id="account-info">
        <div>
            <label>UID:</label>
            <input type="text" name="param[uid]" value="" />
        </div>
        <div>
            <label>ACCESS_TOKEN:</label>
            <input type="text" name="param[token]" value="" />
        </div>
    </div>
 	
    <div>
        <label>接口</label>
        <link rel="stylesheet" href="/css/combo.select.css">
        <select name="param[api]" id="api-selector"   >
            <option>选择测试接口</option>
        <?php foreach ($apis as $api):?>
            <?php if($api['publish'] == 1):?>
            <option value="<?= $api['id']; ?>"><?= $api['label']; ?></option>
			<?php endif;?>
        <?php endforeach;?>
        </select>

    </div>
    <div id="params"></div>
    <div><input class="btn btn-primary" type="button" onclick="formSubmit()" value="测试"/></div>
</form>

<div id="jsonview" style="margin-top: 20px"><?php JsonView::widget();?></div>
<div id="result"></div>
</div>
</div>
<script type="text/javascript">

<?php $this->registerJs('domReady();', View::POS_READY);?>

function domReady()
{
	$("#api-selector").change(function(){
		var name = $(this).val();

		$.ajax({
		   type: "GET",
		   url: "<?= Url::toRoute('sandbox/params')?>",
		   data: "id=" + $(this).val(),
		   dataType: "html",
		   success: function(response){
		     $("#params").html(response);
		   }
		});
	});
}

function apiSelect(obj)
{
	var refid = jQuery(obj.options[obj.selectedIndex]).attr("refid");
	if(refid == 'userLogin' || refid == 'getBusiness' || refid == 'signUp')
	{
		//jQuery("#account-info").hide();
		//jQuery("#account-info").remove();
	}
	else
	{
		jQuery("#account-info").show();
	}
	
	if(refid == null)
	{
		jQuery("#params").html('');
	}
	else
	{
		jQuery("#params").html(paramsHtml[refid+"Html"]);
	}
}

function formSubmit()
{
	$("#testForm").ajaxSubmit({
		dataType:'text',
		success:function(data){
			$("#result").html(data);
			$("#jsontext").val($("#result pre").html());
			g_jsonviewer.format();
	}});
}

</script>