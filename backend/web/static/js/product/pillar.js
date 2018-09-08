/* 柱形图 */
function pillar(object,visitName,arr,datetime,YUnit,checkName){     //对象，主标题名，数据数组，图表名，x轴类名,y轴单位，系列名
		var myChart = echarts.init(object);
	     // 指定图表的配置项和数据
	    var colors=['#11e8e6','#ffb700','#002dbe','#ddd','#fff',/*'#108ac6'*/];  //图表青，图表黄，基线（轴线）深蓝,轴数据#333，标题色        
		var fontSizes=['12','14','16']; 	 
			 option = {	
			 		color:colors,
			 		// backgroundColor: "#f00",
			 		title: {
				        text: visitName,
				        x:'0%',   //水平安放位置
				        y:'0%',
				        textStyle:{
				        	color: colors[4],
		                    fontSize: fontSizes[1],
		                    fontWeight: 'normal'
				        }
				    },			   
				    tooltip: {
				        trigger: 'axis',
						axisPointer : {            // 坐标轴指示器，坐标轴触发有效
				            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
				        }
				    },
				    grid: {          //图表整体位置
			            left: '3%',
			            right: '8%',
			            top:'25%',
			            bottom: '10%',
			            containLabel: true
			        },
				   yAxis: {
				        type: 'value',
				        boundaryGap: [0, 0.01],
				        axisLabel: {
                                show: true,
                                textStyle: {
                                    color: colors[3]
                                },
                                formatter: '{value}'+YUnit  //添加y轴单位
                        },
                        axisLine: {               //基线颜色
			                lineStyle: {
			                    color: colors[2]
			                }
			            },
			            splitLine:{show: false},//去除网格线
			            splitNumber: 2,  //分割段数，不指定时根据min、max算法调整
				    },
				    xAxis: {
				    	splitNumber: 25,
				        type: 'category',
				        data: datetime,
				        axisLabel: {
                                show: true,
                                textStyle: {
                                    color: colors[3]
                                }
                        },
                        axisLine: {
			                lineStyle: {
			                    color: colors[2]
			                }
			            }
				    },
				    series: [
				        {
				            name: checkName,
				            type: 'bar',
				            data: arr,
				            barWidth:12,  //柱形宽度
				        }
				        
				    ],
				    itemStyle: {         //设置渐变颜色
		                normal: {
		                 
		                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
		                        offset: 0,
		                        // color: 'rgba(17, 168,171, 1)'
		                        color: colors[0]
		                    }, {
		                        offset: 1,
		                        color: 'rgba(76,169,235, 0.1)'
		                    }]),
		                    shadowColor: 'rgba(0, 0, 0, 0.1)',
		                    shadowBlur: 10,
		                }
		            }
             };

            //自动触发图表的tooltip行为
	        arr.currentIndex = -1;
	        setInterval(function () {
	        	var dataLen = arr.length;    //length取option.series[0].data数据长度

	            // 取消之前高亮的图形
	            myChart.dispatchAction({
	                type: 'downplay',
	                seriesIndex: 0,
	                dataIndex: arr.currentIndex
	            });

	            arr.currentIndex = (arr.currentIndex + 1) % dataLen;


	            // 高亮当前图形
	            myChart.dispatchAction({
	                type: 'highlight',
	                seriesIndex: 0,
	                dataIndex: arr.currentIndex
	            });
	            // 显示 tooltip
	            myChart.dispatchAction({
	                type: 'showTip',
	                seriesIndex: 0,
	                dataIndex: arr.currentIndex
	            });
	        }, 1000);
                   
            // 使用刚指定的配置项和数据显示图表。
            myChart.setOption(option); 
}