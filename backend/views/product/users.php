<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>用户分布图-运营状况</title>
	<link href="../css/resett.css" rel="stylesheet">
	<link href="../css/product.css" rel="stylesheet">
</head>
<body>
	<div class="wrap">
		<div class="container">
		    <!-- 业务管理 -->
			<div class="usersleft f_left">
				<div class="busintit tit">云媒云管理-<i>用户分布图</i></div>
				<div class="busincont userscont clearfix">
					<div class="userstit">
						<ul>
							<li class="current">采购商</li>
							<li>供货商</li>
							<li>配送商</li>
							<li>生产商</li>
						</ul>
					</div>
					<div class="usersdist">
						<div class="userMap" id="userMap"></div>
					</div>
				</div>
			</div>
            <!-- 考勤管理 -->
			<div class="usersright f_right">
				<div class="checktit tit">云媒云管理-<i>运营状况</i></div>
				<div class="checkcont operacont clearfix">
				    <div class="alliance f_left">
						<h5>联盟商户</h5>
						<div class="box">
							<div class="usermeages" id="userNum"></div>
							<div class="usermeages" id="userMake"></div>
							<div class="usermeages" id="visitFrequency"></div>
						</div>						
					</div>
					<div class="operadata f_right">
						<h5>业务数据</h5>							
						<div class="box">
							<div class="busind" id="checkData"></div>
							<div class="busindTwo clearfix">
								<div class="busind-rate f_left" id="checkRateOld"></div>
								<div class="busind-rate f_left" id="checkRatePrev"></div>
							    <div class="busind-rate f_left" id="checkRateCur"></div>
							</div>							
							<div class="busind" id="checkRateComp"></div>
						</div>
					</div>					
			    </div>
		    </div>
	    </div>
	</div>
</body>
<script src="../js/jQuery-2.1.4.min.js"></script>
<script src="../js/echarts.min.js"></script>

<script type="text/javascript" src="../js/bmap.min.js"></script>
<script src="../js/china.js" type="text/javascript"></script>
<script src="../js/map.js" type="text/javascript"></script>

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

	/* 成员构成 */
	var members={
		"makeName":"成员构成",
		"makeData":[
		       [335,"供应商"],
		       [310,"开发商"],
		       [234,"生产商"]
		],
	}
	

	/* 成员统计 */
	var memberCount={
		"visitName":"成员统计",
		"visitData":[100002,1122,1136,1147,11158,11112,11122,1136,11147,11158],
		"datetime1":["1月","2月","3月","4月","5月","6月","7月","8月","9月","10月"],
		"YUnit":"万"
	};
	// console.log(aaa.visitData);
	// console.log(aaa.datetime1);

	/* 业务数据 */
	var  businessData={
		"checkTit":"2017年业务数据详情",
		"visitName":"数据详情",
		"visitData":[1662,1152,1136,1147],
		"datetime1":["1月","2月","3月","4月"]		
	};

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
	

	/* 用户分布 */
	var userDist={
	    "mapData":[
		 	{name:'台湾',  value:61},  // value：后台传省份名和数值
			{name:'河北',  value:3},
			{name:'山西',  value:1},
			{name:'内蒙古',value:2},
			{name:'辽宁',  value:16},
			{name:'吉林',  value:1},
			{name:'黑龙江',value:2},
			{name:'江苏',  value:1},
			{name:'浙江',  value:45},
			{name:'安徽',  value:3},	
			{name:'福建',  value:18}, 
			{name:'江西',  value:1}, 
			{name:'山东',  value:13}, 
			{name:'河南',  value:1},
			{name:'湖北',  value:3},
			{name:'湖南',  value:3},	
			{name:'广东',  value:24},
			{name:'广西',  value:2},	
			{name:'海南',  value:1},
			{name:'四川',  value:2},
			{name:'贵州',  value:3},
			{name:'云南',  value:1},
			{name:'西藏',  value:1},	
			{name:'陕西',  value:1},
			{name:'甘肃',  value:2},
			{name:'青海',  value:2},	
			{name:'宁夏',  value:2},
			{name:'新疆',  value:1},
			{name:'北京',  value:1},	
			{name:'天津',  value:1},  
			{name:'上海',  value:6},	
			{name:'重庆',  value:2},	
			{name:'香港',  value:5},  
			{name:'澳门',  value:1}
	    ]
	};
	
        map(document.getElementById('userMap'),userDist.mapData); //用户分布
		//对象，柱形图表名，柱形数据数组，背景图数据数组,y轴单位
		pillarBack(document.getElementById('userNum'),userNum.numTit,userNum.numTime,userNum.numData,userNum.numYUnit);  //商户数量
		
		//对象，标题名，数据数组
		annular(document.getElementById('userMake'),members.makeName,members.makeData); //成员构成

        //对象，数据数组，图表名，x轴类名,y轴单位,系列名
		pillar(document.getElementById('visitFrequency'),memberCount.visitName,memberCount.visitData,memberCount.datetime1,memberCount.YUnit,memberCount.visitName);  //成员统计
		pillar(document.getElementById('checkData'),businessData.checkTit,businessData.visitData,businessData.datetime1,memberCount.YUnit,businessData.visitName);  //考勤数据
		// pillar(document.getElementById('checkData'),checkTit,checkData,datetime,YUnit1,checkName);  //考勤数据

		//对象 标题名，当年年份，当年数据，增长数据，类目名
		annularTwo(document.getElementById('checkRateOld'),checkRateOld.checkRateTit,checkRateOld.checkRateYear,checkRateOld.checkRateCurrent,checkRateOld.checkRateIncrease,checkRateOld.checkRateName,"");//业务提升率

		annularTwo(document.getElementById('checkRatePrev'),checkRatePrev.checkRateTit,checkRatePrev.checkRateYear,checkRatePrev.checkRateCurrent,checkRatePrev.checkRateIncrease,checkRatePrev.checkRateName,"");//业务提升率
		annularTwo(document.getElementById('checkRateCur'),checkRateCur.checkRateTit,checkRateCur.checkRateYear,checkRateCur.checkRateCurrent,checkRateCur.checkRateIncrease,checkRateCur.checkRateName,"");//业务提升率

		//对象 x轴类目，柱形数组，折线数组，柱形名，折线名，y轴单位
		lineStack(document.getElementById('checkRateComp'),businDataRate.titname,businDataRate.dataLastYear,businDataRate.dataCurrentYear,businDataRate.datetime,businDataRate.lastYear,businDataRate.currentYear,businDataRate.YUnit); //柱形折现堆叠  业务提升率


		function showChart(){
			$.post('../php/userajax.php',{
				userNum:userNum,
				members:members,
				memberCount:memberCount,
				businessData:businessData,
				checkRateOld:checkRateOld,
				checkRatePrev:checkRatePrev,
				checkRateCur:checkRateCur,
				businDataRate:businDataRate,
				//userDist:userDist
			},function(data){
				console.log(data);
			},"json");
		}

		$(".userstit ul li").on('click',function(){
			$(this).addClass('current').siblings().removeClass('current');
			var num=$(this).index();
			$(".usersdist p").eq(num).css('display','block').siblings().css('display','none');
			var userDistName=$(this).text();
				//console.log(userDist1);
			$.post('../php/mapajax.php',{userDistName:userDistName,userDist:userDist},function(data){
				console.log(data);
			})
		});
</script>	
</html>