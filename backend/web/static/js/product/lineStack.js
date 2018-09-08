function lineStack(object,titname,dataLastYear,dataCurrentYear,datetime,lastYear,currentYear,YUnit){ ////参数：对象，标题名，上年数据，当年数据，x轴类目，上年，当年，y轴单位
	var myChart = echarts.init(object);
	// 指定图表的配置项和数据 
    var colors=['#11e8e6','#ffb700','#002dbe','#fff','#108ac6','#8a0bbd'];  //图表青，图表黄，基线（轴线）深蓝,轴数据#333，标题色 环形紫
	var fontSizes=['12','14','16'];    
	    option = {
		   title: {
		   		show:false,
		        text: titname,
		        x:'0%',   //水平安放位置
		        y:'5%',
		        textStyle:{
		        	color: colors[4],
                    fontSize: fontSizes[1],
                    fontWeight: 'normal'
		        }
		    },
		    tooltip : {
		        trigger: 'axis',
		        axisPointer: {
		            type: 'none',   //悬浮不显示线条
		            label: {
		                backgroundColor: '#6a7985'
		            }
		        }
		    },
		    grid: {
		        left: '3%',
		        right: '4%',
		        top:'10%',
		        bottom: '12%',
		        containLabel: true
		    },
		    xAxis : [
		        {
		            type : 'category',
		            boundaryGap : false,
		            axisLabel: {
                                show: true,
                                textStyle: {
                                    color: colors[3]
                                }
                    },
		            axisLine: {               //基线颜色
			                lineStyle: {
			                    color: colors[2]
			                }
			        },
		            data : datetime
		        }
		    ],
		    yAxis : [
		        {
		            type : 'value',
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
		        }
		    ],
		    series : [     
		        {
		            name:lastYear,
		            type:'line',
		            stack: '总量',
		            symbol:'circle',//拐点样式
                	symbolSize: 5,//拐点大小
		            areaStyle: {normal: {      //堆叠渐变区域
		            	//线性渐变--0,0,0,1从上到下渐变
		            	color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
		                        offset: 0,
		                        // color: 'rgba(17, 168,171, 1)'
		                        color: colors[5]
		                    }, {
		                        offset: 1,
		                        color: 'rgba(138,11,139, 0.3)'
		                    }]),
		            }},
		            itemStyle : {  
                        normal : { 
                        	color: colors[5],  //折点颜色
                            lineStyle:{     //折线线条颜色
                                color:colors[5]  
                            }  
                        }  
                    },     
		            data:dataLastYear
		        },
		        {
		            name:currentYear,
		            type:'line',
		            stack: '总量',		           
		            data:dataCurrentYear,
		            symbol:'circle',//拐点样式
                	symbolSize: 5,//拐点大小
		            itemStyle:{
		            	normal:{
		            		color:colors[0]
		            	}
		            }
		        }
		        
		    ]
		};
		
		//自动触发图表的tooltip行为
        dataLastYear.currentIndex = -1;
        setInterval(function () {
        	var dataLen = dataLastYear.length;    //length取option.series[0].data数据长度

            // 取消之前高亮的图形
            myChart.dispatchAction({
                type: 'downplay',
                seriesIndex: 0,
                dataIndex: dataLastYear.currentIndex
            });

            dataLastYear.currentIndex = (dataLastYear.currentIndex + 1) % dataLen;


            // 高亮当前图形
            myChart.dispatchAction({
                type: 'highlight',
                seriesIndex: 0,
                dataIndex: dataLastYear.currentIndex
            });
            // 显示 tooltip
            myChart.dispatchAction({
                type: 'showTip',
                seriesIndex: 0,
                dataIndex: dataLastYear.currentIndex
            });
        }, 1000);

    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);
}