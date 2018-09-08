<?php
use app\models\Api;
use yii\helpers\Url;
?>
<!--名称-->
<h2 class="fixmt"><?= $api->name;?></h2>
<div class="contentSub">
	<div id="bodyContent">
		<table id="toc" class="toc" summary="目录">
			<tbody>
				<tr>
					<td>
						<div id="toctitle">
							<h2>目录</h2> 
							<span class="toctoggle">
								[<a id="togglelink" class="internal" href="javascript:toggleToc()">隐藏</a>]
							</span>
						</div>
						<ul>
							<li class="toclevel-1"><a href="#What.27s_New.3F"><span class="tocnumber">1</span> <span class="toctext">What's New?</span></a></li>
							<li class="toclevel-1"><a href="#1_.E5.8A.9F.E8.83.BD.E8.AF.B4.E6.98.8E"><span class="tocnumber">2</span> <span class="toctext">1 功能说明</span></a></li>
							<li class="toclevel-1"><a href="#2_.E6.8E.A5.E5.8F.A3.E8.B0.83.E7.94.A8.E8.AF.B4.E6.98.8E"><span class="tocnumber">3</span> <span class="toctext">2 接口调用说明</span></a>
								<ul>
									<li class="toclevel-2"><a href="#2.1.09URL"><span class="tocnumber">3.1</span> <span class="toctext">2.1	URL</span></a></li>
									<li class="toclevel-2"><a href="#2.2.09.E6.A0.BC.E5.BC.8F"><span class="tocnumber">3.2</span> <span class="toctext">2.2	格式</span></a></li>
									<li class="toclevel-2"><a href="#2.3.09HTTP.E8.AF.B7.E6.B1.82.E6.96.B9.E5.BC.8F"><span class="tocnumber">3.3</span> <span class="toctext">2.3	HTTP请求方式</span></a></li>
									<li class="toclevel-2"><a href="#2.5.09.E8.BE.93.E5.85.A5.E5.8F.82.E6.95.B0.E8.AF.B4.E6.98.8E"><span class="tocnumber">3.5</span> <span class="toctext">2.5	输入参数说明</span></a></li>
									<li class="toclevel-2"><a href="#2.6.09.E8.AF.B7.E6.B1.82.E7.A4.BA.E4.BE.8B"><span class="tocnumber">3.6</span> <span class="toctext">2.6	请求示例</span></a></li>
									<li class="toclevel-2"><a href="#2.7.09.E8.BF.94.E5.9B.9E.E5.8F.82.E6.95.B0.E8.AF.B4.E6.98.8E"><span class="tocnumber">3.7</span> <span class="toctext">2.7	返回参数说明</span></a></li>
									<li class="toclevel-2"><a href="#2.8_.E9.94.99.E8.AF.AF.E8.BF.94.E5.9B.9E.E7.A0.81.E8.AF.B4.E6.98.8E"><span class="tocnumber">3.8</span> <span class="toctext">2.8 错误返回码说明</span></a></li>
									<li class="toclevel-2"><a href="#2.9.09.E6.AD.A3.E7.A1.AE.E8.BF.94.E5.9B.9E.E7.A4.BA.E4.BE.8B"><span class="tocnumber">3.9</span> <span class="toctext">2.9	正确返回示例</span></a></li>
									<li class="toclevel-2"><a href="#2.10.09.E9.94.99.E8.AF.AF.E8.BF.94.E5.9B.9E.E7.A4.BA.E4.BE.8B"><span class="tocnumber">3.10</span> <span class="toctext">2.10	错误返回示例</span></a></li>
							</ul>
						</li>
						
						<li class="toclevel-1"><a href="#3_.E7.A4.BA.E4.BE.8B.E4.BB.A3.E7.A0.81"><span class="tocnumber">4</span> <span class="toctext">3 示例代码</span></a></li>
						<li class="toclevel-1"><a href="#4_.E6.8E.A5.E5.8F.A3.E8.B0.83.E8.AF.95"><span class="tocnumber">5</span> <span class="toctext">4 接口调试</span></a></li>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>
		<a name="What.27s_New.3F"></a>
		<h2> <span class="mw-headline">What's New?</span></h2>
		<p><?= $api->label;?><br></p>	
		<a name="1_.E5.8A.9F.E8.83.BD.E8.AF.B4.E6.98.8E"></a>
		<h2> <span class="mw-headline">1 功能说明 </span></h2>
		<p>
			<?= $api->description;?>
		</p>
		
		<a name="2_.E6.8E.A5.E5.8F.A3.E8.B0.83.E7.94.A8.E8.AF.B4.E6.98.8E"></a>
		<h2> <span class="mw-headline">2 接口调用说明</span></h2>
		<a name="2.1.09URL" id="2.1.09URL"></a>
		<h3> <span class="mw-headline">2.1	URL</span></h3>
		<p>http://[域名]/<?= $api->name;?>
			<br><br>
			正式环境域名或测试环境IP详见：<a href="/wiki/API3.0%E6%96%87%E6%A1%A3#.E8.AF.B7.E6.B1.82URL.E8.AF.B4.E6.98.8E" title="API3.0文档">API3.0文档#请求URL说明</a>。
		</p>
		<a name="2.2.09.E6.A0.BC.E5.BC.8F" id="2.2.09.E6.A0.BC.E5.BC.8F"></a>
		<h3> <span class="mw-headline">2.2	格式</span></h3>
		<p>json</p>
		<a name="2.3.09HTTP.E8.AF.B7.E6.B1.82.E6.96.B9.E5.BC.8F" id="2.3.09HTTP.E8.AF.B7.E6.B1.82.E6.96.B9.E5.BC.8F"></a>
		<h3> <span class="mw-headline">2.3	HTTP请求方式</span></h3>
		<p><?= Api::methodLabel($api->method);?></p>
		<a name="2.5.09.E8.BE.93.E5.85.A5.E5.8F.82.E6.95.B0.E8.AF.B4.E6.98.8E" id="2.5.09.E8.BE.93.E5.85.A5.E5.8F.82.E6.95.B0.E8.AF.B4.E6.98.8E"></a>
		<h3> <span class="mw-headline">2.5	输入参数说明</span></h3>
		<p>
			<font color="red">各个参数请进行URL 编码，编码时请遵守 
			<a href="http://tools.ietf.org/html/rfc1738" class="external" title="http://tools.ietf.org/html/rfc1738" target="_blank">RFC 1738</a>
			</font><br>
		</p>
		<p>（1）公共参数<br>
		发送请求时必须传入公共参数，详见<a href="/wiki/API3.0%E6%96%87%E6%A1%A3#.E5.85.AC.E5.85.B1.E5.8F.82.E6.95.B0.E8.AF.B4.E6.98.8E" title="API3.0文档">公共参数说明</a>。<br><br> 
		</p>
		<p>（2）私有参数<br></p>
		<table class="t">
			<tbody>
				<tr>
					<th width="10%"><b>参数名称</b></th>
					<th width="10%"><b>是否必须</b></th>
					<th width="10%"><b>类型</b></th>
					<th> <b>描述</b></th>
				</tr>
				<?php foreach ($api->inParams as $param):?>
				<tr>
					<td> <b><?= $param->name;?></b></td>
					<td><font color="red"><?= $param->request ? '是' : ''?></font></td>
					<td> <?= $param->pt->name;?></td>
					<td>  <?= $param->label.'<br />'.$param->desc;?>
					</td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>
		<a name="2.6.09.E8.AF.B7.E6.B1.82.E7.A4.BA.E4.BE.8B" id="2.6.09.E8.AF.B7.E6.B1.82.E7.A4.BA.E4.BE.8B"></a>
		<h3> <span class="mw-headline">2.6	请求示例</span></h3>
		<div class="code">
			<p>http://<?= Yii::$app->params['api'][$api->module_id]['domain']?>/<?= $api->name?>?<br />
			<?= $api->example?>
			</p>
		</div>
		<a name="2.7.09.E8.BF.94.E5.9B.9E.E5.8F.82.E6.95.B0.E8.AF.B4.E6.98.8E" id="2.7.09.E8.BF.94.E5.9B.9E.E5.8F.82.E6.95.B0.E8.AF.B4.E6.98.8E"></a>
		<h3> <span class="mw-headline">2.7	返回参数说明</span></h3>
		<table class="t">
			<tbody>
				<tr>
					<th width="20%"><b>参数名称</b></th>
					<th><b>描述</b></th>
				</tr>
				<tr> 
					<td> <b>ret</b></td>
					<td> 返回码。详见<a href="<?=Url::to('code')?>" title="公共返回码说明">公共返回码说明#OpenAPI V3.0 返回码</a>。</td>
				</tr>
				<?php foreach ($api->outputParams as $param):?>
				<tr>
					<td> <b><?= $param->name?></b></td>
					<td><pre><?= $param->desc?></pre></td>
				</tr>
				<?php endforeach;?>
			</tbody>
		</table>
		<a name="2.8_.E9.94.99.E8.AF.AF.E8.BF.94.E5.9B.9E.E7.A0.81.E8.AF.B4.E6.98.8E" id="2.8_.E9.94.99.E8.AF.AF.E8.BF.94.E5.9B.9E.E7.A0.81.E8.AF.B4.E6.98.8E"></a>
		<h3> <span class="mw-headline">2.8 错误返回码说明</span></h3>
		<p>公共错误返回码：<a href="/wiki/%E5%85%AC%E5%85%B1%E8%BF%94%E5%9B%9E%E7%A0%81%E8%AF%B4%E6%98%8E#OpenAPI_V3.0_.E8.BF.94.E5.9B.9E.E7.A0.81" title="公共返回码说明">公共返回码说明#OpenAPI V3.0 返回码</a>。
			<br>
			本接口私有错误返回码：暂无。
			<br><br>
		</p>
		<a name="2.9.09.E6.AD.A3.E7.A1.AE.E8.BF.94.E5.9B.9E.E7.A4.BA.E4.BE.8B" id="2.9.09.E6.AD.A3.E7.A1.AE.E8.BF.94.E5.9B.9E.E7.A4.BA.E4.BE.8B"></a>
		<h3> <span class="mw-headline">2.9	正确返回示例</span></h3>
		<p>JSON示例:</p>
		<div class="code">
			<pre><?= $api->response ?: '{
"ret":0
}'?>
</pre>
		</div>
		<a name="3_.E7.A4.BA.E4.BE.8B.E4.BB.A3.E7.A0.81" id="3_.E7.A4.BA.E4.BE.8B.E4.BB.A3.E7.A0.81"></a>
		<!-- 
		<h2> <span class="mw-headline">3 示例代码</span></h2>
		<p>您可以直接下载并使用腾讯开放平台提供的SDK，并参考SDK里面的给出的示例代码进行接口调用。<br>
			详见：
			<a href="/wiki/SDK%E4%B8%8B%E8%BD%BD#OpenAPI_V3.0_SDK.E4.B8.8B.E8.BD.BD" title="SDK下载">SDK下载#OpenAPI V3.0 SDK下载</a>。
		</p>
		 -->

	</div>
</div>
