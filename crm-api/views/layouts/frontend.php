<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\assets\FrontendAsset;
use yii\helpers\Url;
use app\models\ApiModule;

FrontendAsset::register($this);
?>
<?php $this->beginPage() ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<?= Html::csrfMetaTags() ?>
<title>帮助与文档</title>
<?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrapper">
	<div class="help-search">
		<div class="y-row">
			<div class="y-span3 title">帮助与文档</div>
			<div class="y-span9 y-last">
		    	<div class="search">
					<input id="search" placeholder="请简单输入您的关键词，如“云服务器”" autocomplete="off" disableautocomplete="">
					<a class="btn search-all-btn" href="javascript:;">搜全部</a>
					<a class="btn search-product-btn" href="javascript:;" style="display: none;">搜本产品</a>
		    	</div>
		    	<div class="hot-search">
					<label>搜索热词：</label>
					<ul class="y-clear">
			 			<li class="y-left"><a href="">远程连接服务器</a></li>
						<li class="y-left"><a href="">挂载数据盘</a></li>
						<li class="y-left"><a href="">域名解析</a></li>
						<li class="y-left"><a href="">ssd云盘</a></li>
						<li class="y-left"><a href="">实名认证</a></li>
						<li class="y-left"><a href="">忘记密码</a></li>    
					</ul>
		    	</div>
		 	</div>
	 	</div>
	</div>
	<!--end help-search-->
	<div class="help-tab-box">
		<div class="y-row y-clear">
			<ul class="y-left y-clear">
				<li class="y-left action">
					<a href="<?= Url::toRoute('api/index')?>">接口中心</a>
				</li>
				<?php $modules = ApiModule::find()->all();?>
				<?php foreach ($modules as $module):?>
				<li class="y-left">
					<a href="<?= Url::toRoute(['api/index', 'm'=>$module->name])?>"><?= $module->label?></a>
				</li>
				<?php endforeach;?>
				<li class="y-left">
					<a href="">联系客服</a>
				</li> 
	   
			</ul>
			<ul class="y-right y-clear">
			    <li class="y-left">
					<a class="tab" href="">
						<img src="/images/tool1.png"/>开发者资源
					</a>
				</li>
				<li class="y-left">
					<a class="tab" href="">
						<img src="/images/tool2.png">社区问答
					</a>
				</li>
				<li class="y-left">
					<a class="tab" href="">
						<img src="/images/tool3.png">上云培训
					</a>
				</li> 
		  
			</ul>
	  	</div>
	</div>
	<!--end help-tab-box-->
	<div class="help-body y-row"><?= $content ?></div>
	<div id="guid-280222" class="aliyun-lego-www-common-register J_Module">
		<div class="module-wrap J_tb_lazyload">
			<div class="y-row" id="J_homePageRegister" style="display: block;">
				<p style="border-top:0;padding:32px 0" class="y-align-center home-page-register">
					<a target="_blank" href="" class="y-btn-blue">免费注册，享新手礼包</a>
				</p>
			</div>
		</div>
	</div>
	<div class="knight-footer">
  		<!--footer-->
		<div class="copyright-100 " data-spm="100">
			<div data-spm="25" class="y-row copyright">
			 	<p class="big">
					<a href="" target="_blank">关于我们</a>
						
					<a href="" target="_blank">法律声明</a>
						
					<a href="" target="_blank">廉正举报</a>
						
					<a href="" target="_blank">友情链接</a>
						
				 </p>
				 <p class="link-wrap">
						
					<a class="link-item" href="" target="_blank">讯猫</a>
						
				</p>
				<p class="copyright">
					© 2009-2016 Aliyun.com 版权所有 ICP证：浙B2-20080101
					<span>
						 <a href="" target="_blank">
							<img src="/images/huizhang.png" style="display:inline-block;width: 22px;">
						 </a>
						 <a href="" target="_blank">
							<img src="/images/djcp.png" style="display:inline-block;width: 22px;">
						 </a>
					</span>
				</p>
			</div>
		</div>
	</div>
	<div class="float-tool">
		<div class="cloudHelper" style="cursor: pointer;">
			<a id="J_cloud"></a>
		</div>
		<div class="goTop"></div>
	</div>
</div>

<?php $this->endBody() ?>
        <script src="/js/jquery.combo.select.js"></script>
        <script>
        $(function() {
        	$('select').comboSelect();
        });
		</script>
</body>
</html>
<?php $this->endPage() ?>