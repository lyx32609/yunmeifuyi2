<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>数据管理</title>
	<link href="../css/resett.css" rel="stylesheet">
	<link href="../css/product.css" rel="stylesheet">
</head>
<body>
	<div class="wrap managebg">
		<div class="container">
		    <!-- 配送联盟 -->
		    <div class="managetit tit">云媒云管理-<i>数据管理</i></div>
			<div class="contleft">				
				<div class="busincont datashare">
					<h5 class="center">数据共享</h5>
					<img src="../images/manage01.png" width="90%" height="90%">
				</div>
			</div>
            <!-- 业务计划 -->
			<div class="contright">
				<div class="checkcont">
					<div class="records">
						<h5>数据中心</h5>
						<div class="usermeages datacenter" id="statistics"></div>
						<div class="usermeages datacenter" id="analyse"></div>
						<div class="usermeages datacenter" id="reuse"></div>
					</div>
			    </div>
		    </div>
	    </div>
	</div>
</body>
<script src="../js/jQuery-2.1.4.min.js"></script>
<script src="../js/echarts.min.js"></script>
<script src="../js/pillarBack.js" type="text/javascript"></script>
<script src="../js/scatter.js" type="text/javascript"></script>
<script src="../js/ecStat.min.js" type="text/javascript"></script>
<script src="../js/barLine.js" type="text/javascript"></script>
<script type="text/javascript">
		window.onload=function(){
			showChart();
		}

    	/* 数据统计 */
		var userNum={
			"numTit":"数据统计",
			"numData":[10002,22,36,47,1018],
			"numTime":["2013年","2014年","2015年","2016年","2017年"],
			"numYUnit":"家",
		}


		/* 数据分析 */
		var analyseData={
			 "numTit":"数据分析",			 
			 "numData":[
					[1611.2, 1511.6,"1月"],
		            [1167.5, 59.0,"2月"], 
		            [159.5, 49.2,"3月"]
			],
			"YUnit":"万", //单位
		}		

		/* 数据利用 */
		var dataReleuse={
			 "numTit":"数据利用",
			 "dataxAxis":["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月","11月","12月"], 
			 "bardata": [6709,4917,3455,2610,2719,1433,1544,1285,508,372,284,178],
			 "linedata": [709,917,1455,2610,3719,4433,5544,6285,7208,8372,9484,9678],
			 "YUnit":"万", //单位
			 "barname":"成本",
			 "linename":"产出",
			 "YUnit":"万", //单位
		}
		
	    

	    //对象，柱形图表名，柱形数据数组，背景图数据数组,y轴单位
		pillarBack(document.getElementById('statistics'),userNum.numTit,userNum.numTime,userNum.numData,userNum.numYUnit);  //数据统计
	    
	    //对象,主标题,散点数据，y轴单位
	    scatter(document.getElementById('analyse'),analyseData.numTit,analyseData.numData,analyseData.YUnit);   //数据分析

	    //对象 x轴类目，柱形数组，折线数组，柱形名，折线名，y轴单位
		barLine(document.getElementById('reuse'),dataReleuse.numTit,dataReleuse.dataxAxis,dataReleuse.bardata,dataReleuse.linedata,dataReleuse.barname,dataReleuse.linename,dataReleuse.YUnit);//柱形折现堆叠  数据利用
    
	   function showChart(){
			$.post('../php/manageajax.php',{
				userNum:userNum,
				analyseData:analyseData,
				dataReleuse:dataReleuse,
			},function(data){
				console.log(data);
			},"json");
		}

</script>	
</html>