<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>配送联盟-业务计划</title>
	<link href="../css/resett.css" rel="stylesheet">
	<link href="../css/product.css" rel="stylesheet">
</head>
<body>
	<div class="wrap">
		<div class="container">
		    <!-- 配送联盟 -->
			<div class="contleft clearfix">
				<div class="busintit tit">云媒云管理-<i>配送联盟</i></div>
				<div class="busincont clearfix">
					<div class="visit f_left">
						<h5 class="center">联盟分布</h5>
						<img src="../images/delivery01.png" alt="" width="90%" height="100%">
					</div>
					<div class="path f_left">
						<h5 class="center">配送路线</h5>
						<img src="../images/delivery01.png" alt="" width="90%" height="90%">
					</div>
					<div class="messages f_left">
						<h5>商户信息</h5>
						<div class="box">
							<div class="usermeages" id="userNum"></div>
							<div class="usermeages" id="userMake"></div>
							<div class="usermeages" id="visitFrequency"></div>
						</div>						
					</div>
				</div>
			</div>
            <!-- 业务计划 -->
			<div class="contright">
				<div class="checktit tit">云媒云管理-<i>业务计划</i></div>
				<div class="checkcont">
					<div class="check">
						<h5>业务数据</h5>
						<div class="busindata" id="checkData"></div>
					</div>
					<div class="checkrate">
						<div class="businrate">
							<div class="growth-rate f_left" id="checkRateOld"></div>
							<div class="growth-rate f_left" id="checkRatePrev"></div>
						    <div class="growth-rate f_left" id="checkRateCur"></div>				    
						</div>
						<div class="times">
					    	<span>2015</span>
					    	<span>2016</span>
					    	<span>2017</span>
					    </div>
					</div>
			    </div>
		    </div>
	    </div>
	</div>
</body>
<script src="../js/jQuery-2.1.4.min.js"></script>
<script src="../js/echarts.min.js"></script>
<script src="../js/pillarBack.js" type="text/javascript"></script>
<script src="../js/annular.js" type="text/javascript"></script>
<script src="../js/pillar.js" type="text/javascript"></script>
<script src="../js/annualarTwo.js" type="text/javascript"></script>
<script src="../js/lineStack.js" type="text/javascript"></script>
<script type="text/javascript">
window.onload=function(){
	showChart();
}

	/* 用户数量 */
	var userNum={
		"numTit":"商户数量",
		// "numNamesub":"(单位：家/年份)",
		"numData":[10002,22,36,47,1018],
		"numTime":["2013年","2014年","2015年","2016年","2017年"],
		"numYUnit":"家",

	}

	/* 联盟商品 */
	var members={
		"makeName":"联盟商品",
		"makeData":[
		       [335,"供应商"],
		       [310,"开发商"],
		       [234,"生产商"]
		],
	}

	/* 效益增长 */
	var memberCount={
		"visitName":"效益增长",
		"visitData":[100002,1122,1136,1147,11158,11112,11122,1136,11147,11158],
		"datetime1":["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月"],
		"YUnit":"万"
	};

	/* 业务数据 */
	var  businessData={
		"checkTit":"2017年业务数据详情",
		"visitName":"数据详情",
		"visitData":[1662,1152,1136,1147],
		"datetime1":["1月","2月","3月","4月"]		
	};

	//业务数据提升率
	//业务提升率
	var checkRateOld={
		"checkRateTit":"",	
	 	"checkRateName":"业务提升率",
	 	"checkRateYear":"2017年",
	 	"checkRateCurrent":200,  //当年数值
	 	"checkRateIncrease":100, //当年比上年的增长量：当年-上年    增长率：100/300*100
	};
	var checkRatePrev={
		"checkRateTit":"",	
	 	"checkRateName":"业务提升率",
	 	"checkRateYear":"2017年",
	 	"checkRateCurrent":300,  //当年数值
	 	"checkRateIncrease":100, //当年比上年的增长量：当年-上年    增长率：100/300*100
	};
	var checkRateCur={
		"checkRateTit":"",	
	 	"checkRateName":"业务提升率",
	 	"checkRateYear":"2017年",
	 	"checkRateCurrent":500,  //当年数值
	 	"checkRateIncrease":50, //当年比上年的增长量：当年-上年    增长率：100/300*100
	};
	//对象，柱形图表名，柱形数据数组，背景图数据数组,y轴单位
		pillarBack(document.getElementById('userNum'),userNum.numTit,userNum.numTime,userNum.numData,userNum.numYUnit);  //商户数量
		
		//对象，标题名，数据数组
		annular(document.getElementById('userMake'),members.makeName,members.makeData); //用户构成

        //对象，主标题名，数据数组，图表名，x轴类名,y轴单位,系列名
		pillar(document.getElementById('visitFrequency'),memberCount.visitName,memberCount.visitData,memberCount.datetime1,memberCount.YUnit,memberCount.visitName);  //回访频率
		pillar(document.getElementById('checkData'),businessData.checkTit,businessData.visitData,businessData.datetime1,memberCount.YUnit,businessData.visitName);  //考勤数据

		//对象 标题名，当年年份，当年数据，增长数据，类目名
		annularTwo(document.getElementById('checkRateOld'),checkRateOld.checkRateTit,checkRateOld.checkRateYear,checkRateOld.checkRateCurrent,checkRateOld.checkRateIncrease,checkRateOld.checkRateName,"");//考勤提升率
		annularTwo(document.getElementById('checkRatePrev'),checkRatePrev.checkRateTit,checkRatePrev.checkRateYear,checkRatePrev.checkRateCurrent,checkRatePrev.checkRateIncrease,checkRatePrev.checkRateName,"");//考勤提升率
		annularTwo(document.getElementById('checkRateCur'),checkRateCur.checkRateTit,checkRateCur.checkRateYear,checkRateCur.checkRateCurrent,checkRateCur.checkRateIncrease,checkRateCur.checkRateName,"");//考勤提升率


		function showChart(){
			$.post('../php/deliveryajax.php',{
				userNum:userNum,
				members:members,
				memberCount:memberCount,
				businessData:businessData,
				checkRateOld:checkRateOld,
				checkRatePrev:checkRatePrev,
				checkRateCur:checkRateCur
			},function(data){
				console.log(data);
			},"json");
		}

</script>	
</html>