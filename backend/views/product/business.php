<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>业务管理-考勤管理</title>
	<link href="../css/resett.css" rel="stylesheet">
	<link href="../css/product.css" rel="stylesheet">
</head>
<body>
	<div class="wrap">
		<div class="container">
		    <!-- 业务管理 -->
			<div class="contleft clearfix">
				<div class="busintit tit">云媒云管理-<i>业务管理</i></div>
				<div class="busincont clearfix">
					<div class="visit f_left">
						<h5 class="center">业务拜访</h5>
						<img src="../images/business01.png" alt="" width="90%" height="100%">
					</div>
					<div class="path f_left">
						<h5 class="center">拜访路线</h5>
						<img src="../images/business02.png" alt="" width="90%" height="90%">
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
            <!-- 考勤管理 -->
			<div class="contright">
				<div class="checktit tit">云媒云管理-<i>考勤管理</i></div>
				<div class="checkcont">
					<div class="check">
						<h5>考勤数据</h5>
						<div class="checkdata" id="checkData"></div>
					</div>
					<div class="checkrate">
						<h5>考勤提升率</h5>
						<div class="ratedata">
							<div class="growth-rate f_left" id="checkRateOld"></div>
							<div class="growth-rate f_left" id="checkRatePrev"></div>
						    <div class="growth-rate f_left" id="checkRateCur"></div>
						</div>
						<div class="checkdata" id="checkRateComp"></div>
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

	/* 用户构成 */
	var members={
		"makeName":"用户构成",
		"makeData":[
		       [335,"供应商"],
		       [310,"开发商"],
		       [234,"生产商"]
		],
	}

	/* 回访频率 */
	var memberCount={
		"visitName":"回访频率",
		"visitData":[100002,1122,1136,1147],
		"datetime1":["1月","2月","3月","4月"],
		"YUnit":"万"
	};

	/* 考勤数据 */
	var  businessData={
		"checkTit":"2017年业务数据详情",
		"visitName":"数据详情",
		"visitData":[1662,1152,1136,1147],
		"datetime1":["1月","2月","3月","4月"]		
	};

	//考勤提升率
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


    //折线
	 var businDataRate={
    	"titname":"业务数据增长",
	 	"dataLastYear":[1150, 1232, 1201, 1154, 1190, 1330, 1410],
	 	"dataCurrentYear":[220, 182, 191, 234, 290, 330, 310],
	 	"datetime":['1月','2月','3月','4月','5月','6月','7月'],
	 	"lastYear":"2016年",
	 	"currentYear":"2017年",
	 	"YUnit":"万", //单位
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

		//对象 x轴类目，柱形数组，折线数组，柱形名，折线名，y轴单位
		lineStack(document.getElementById('checkRateComp'),businDataRate.titname,businDataRate.dataLastYear,businDataRate.dataCurrentYear,businDataRate.datetime,businDataRate.lastYear,businDataRate.currentYear,businDataRate.YUnit); //柱形折现堆叠  考勤提升率
    
	    function showChart(){
			$.post('../php/businajax.php',{
				userNum:userNum,
				members:members,
				memberCount:memberCount,
				businessData:businessData,
				checkRateOld:checkRateOld,
				checkRatePrev:checkRatePrev,
				checkRateCur:checkRateCur,
				businDataRate:businDataRate
			},function(data){
				console.log(data);
			},"json");
		}
</script>	
</html>