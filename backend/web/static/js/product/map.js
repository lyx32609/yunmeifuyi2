function map(object,mapData){
	var myChart = echarts.init(object);
     // 指定图表的配置项和数据
    var colors=['#11e8e6','#ffb700','#002dbe','#ddd','#fff',/*'#108ac6'*/];  //图表青，图表黄，基线（轴线）深蓝,轴数据#333，标题色        
	var fontSizes=['12','14','16']; 	 

    var data=mapData;
	var geoCoordMap = {
        '台湾':[121.509062, 25.044332],			
		'河北': [114.502461, 38.045474],		
		'山西': [112.549248, 37.857014],		
		'内蒙古': [111.670801, 40.818311],		
		'辽宁': [123.429096, 41.796767],		
		'吉林': [125.3245, 43.886841],			
		'黑龙江': [126.642464, 45.756967],		
		'江苏': [118.767413, 32.041544],		
		'浙江': [120.153576, 30.287459],		
		'安徽': [117.283042, 31.86119],			
		'福建': [119.306239, 26.075302],      	
		'江西': [115.892151, 28.676493],      	
		'山东': [117.000923, 36.675807],      	
		'河南': [113.665412, 34.757975],		
		'湖北': [114.298572, 30.584355],		
		'湖南': [112.982279, 28.19409],			
		'广东': [113.280637, 23.125178],		
		'广西': [108.320004, 22.82402],			
		'海南': [110.33119, 20.031971],			
		'四川': [104.065735, 30.659462],		
		'贵州': [106.713478, 26.578343],		
		'云南': [102.712251, 25.040609],		
		'西藏': [91.132212, 29.660361],			
		'陕西': [108.948024, 34.263161],		
		'甘肃': [103.823557, 36.058039],		
		'青海':[101.778916, 36.623178],			
		'宁夏':[106.278179, 38.46637],			
		'新疆':[87.617733, 43.792818],			
		'北京':[116.405285, 39.904989],			
		'天津':[117.190182, 39.125596],         
		'上海':[121.472644, 31.231706],			
		'重庆':[106.504962, 29.533155],			
		'香港':[114.173355, 22.320048],         
		'澳门':[113.54909, 22.198951],			
    };

	var convertData = function(data) {
	    var res = [];
	    for (var i = 0; i < data.length; i++) {
	    	var geoCoord = geoCoordMap[data[i].name];   //经纬度
	    	    //console.log(geoCoord);
	        if (geoCoord) {
	            res.push({
	                name: data[i].name,
	                value: geoCoord.concat(data[i].value)
	            });
	        }
	    }
	    return res;
	};

	var convertedData = [
	    convertData(data),
	    convertData(data.sort(function(a, b) {
	        return b.value - a.value;
	    }).slice(0, 6))   //截取后仅后六个显示闪烁效果失效
	];
	data.sort(function(a, b) {
	    return a.value - b.value;
	})

	var selectedItems = [];
	var categoryData = [];
	var barData = [];
	//   var maxBar = 30;
	var sum = 0;
	var count = data.length;
	for (var i = 0; i < data.length; i++) {
	    categoryData.push(data[i].name);
	    barData.push(data[i].value);
	    sum += data[i].value;
	}
	//console.log(categoryData);
	console.log(sum + "   " + count)
	option = {
	    title: {
	        text: '',
	        left: 'center',
	        textStyle: {
	            color: '#fff'
	        }
	    }, 
	    geo: {
	        map: 'china',
	        top:'80',
	        left: '0',
	        right: '33%',
	        //center: [117.98561551896913, 31.205000490896193],
	        center: [93.98561551896913, 35.205000490896193],
	        zoom: 1.5,
	        label: {
	            emphasis: {
	                show: false
	            }
	        },
	        roam: true,
	        itemStyle: {
	            normal: {
	                areaColor: '#00366e', //地图板块色
	                borderColor: '#105c8e',
	            },
	            emphasis: {
	                areaColor: '#0d529a'  //悬浮颜色
	            }
	        }
	    },
	    tooltip: {
	        trigger: 'item'
	    },
	    series: [
	    {
	        //  name: 'Top 5',
	        type: 'effectScatter',
	        coordinateSystem: 'geo',
	        data: convertedData[0],
	        symbolSize:10,
	        showEffectOn: 'render',
	        rippleEffect: {
	             period: 4,
                brushType: 'stroke',
                scale: 4
	        },
	        hoverAnimation: true,
	        label: {
	            normal: {
	                formatter: '{b}',
	                position: 'top',
	                show: true,
	                textStyle: {
                          color: "#008fec",   //散点提示文字颜色
                          fontSize:fontSizes[0],
                    },
	            }
	        },
	        itemStyle: {
	            normal: {
	                color: "#0600ff",  //散点颜色
	                shadowBlur: 30,
	                shadowColor: "#012b8b",

	            }
	        },
	        zlevel: 1
	    }]
	};

    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option); 
}