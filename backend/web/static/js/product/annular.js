/* 环形图 */
function annular(object,titname,annularData){   //对象，标题名，数据数组
	var myChart = echarts.init(object);
	    // 指定图表的配置项和数据
	    var colors=['#f6cb26','#014de1','#8a0bbd','#ddd','#fff'/*'#108ac6'*/];  //环图表黄，环图表蓝，环图表紫,类目数据色 标题色
		var fontSizes=['12','14','16']; 
		option = {
			color:colors,
			title: {
		        text: titname,
		        x:'0%',   //水平安放位置
		        y:'0%',
		        textStyle:{
		        	color: colors[4],
                    fontSize: fontSizes[1],
                    fontWeight: 'normal'
		        }
		    },
		    tooltip: {
		    	show:true,
		        trigger: 'item',
		        formatter: "{a} <br/>{b}: {c} ({d}%)",  //图表名 类目名  数值 数值百分比

		    },
		    series: [
		        {
		            name:titname,
		            type:'pie',
		            radius: ['26%', '46%'],
		            avoidLabelOverlap: true,
		            label: {   //标签，饼图默认显示在外部，离饼图距离由labelLine.length决定， 
		                normal: {
		                	textStyle:{    //修改类目文字颜色
		                	 	color:colors[3]   
		                	},
		                    formatter: '{b}\n{d}%'   //默认显示视觉引导线+类目名+百分比
		                }
		            },
		            labelLine:{
		            	normal: {
		                	length:0,
		                }
		            },
        			data:[
					        {value:annularData[0][0], name:annularData[0][1]},
					        {value:annularData[1][0], name:annularData[1][1]},
					        {value:annularData[2][0], name:annularData[2][1]}
					],
					itemStyle: {
		                emphasis: {
		                    shadowBlur: 10,
		                    shadowOffsetX: 0,
		                    shadowColor: 'rgba(0, 0, 0, 1)',		                    
		                }
		            },													
		        }
		    ]
		};

		//自动触发图表的tooltip行为
        annularData.currentIndex = -1;
        setInterval(function () {
        	var dataLen = annularData.length;    //length取option.series[0].data数据长度

            // 取消之前高亮的图形
            myChart.dispatchAction({
                type: 'downplay',
                seriesIndex: 0,
                dataIndex: annularData.currentIndex
            });

            annularData.currentIndex = (annularData.currentIndex + 1) % dataLen;


            // 高亮当前图形
            myChart.dispatchAction({
                type: 'highlight',
                seriesIndex: 0,
                dataIndex: annularData.currentIndex
            });
            // 显示 tooltip
            myChart.dispatchAction({
                type: 'showTip',
                seriesIndex: 0,
                dataIndex: annularData.currentIndex
            });
        }, 1000);
		// 使用刚指定的配置项和数据显示图表。
        myChart.setOption(option); 
}