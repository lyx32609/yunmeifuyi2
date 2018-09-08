function pillarBack(object,numTit,numTime,numData,numYUnit){  //对象，柱形图表名，柱形数据数组，背景图数据数组,y轴单位
    var myChart = echarts.init(object);
        // 指定图表的配置项和数据
        var colors=['#11e8e6','#ffb700','#002dbe','#ddd','#fff',/*'#108ac6'*/'rgba(0, 0, 0,0.2)'];  //图表青，图表黄，基线（轴线）深蓝,轴数据#333,标题色，柱形图表透明背景
        var fontSizes=['12','14','16'];    
            option = {  
                color:colors,             
                animation: false,
                title: {
                    text: numTit,
                    x:'0%',   //水平安放位置
                    y:'0%',
                    textStyle:{
                        color: colors[4],
                        fontSize: fontSizes[1],
                        fontWeight: 'normal'
                    },
                },
                tooltip: {
                    trigger: 'axis',
                    axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                        type : 'shadow',        // 默认为直线，可选为：'line' | 'shadow'
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
                    axisLabel: {
                                show: true,
                                textStyle: {
                                    color: colors[3]
                                },
                                formatter: '{value}'+numYUnit  //添加y轴单位
                    },
                    axisLine: {               //基线颜色
                            lineStyle: {
                                color: colors[2]
                            }
                    },
                    splitLine:{show: false},//去除网格线
                    splitNumber: 2,  //分割段数，不指定时根据min、max算法调整
                },
                xAxis:[ 
                        {
                            splitNumber: 15,
                            type : 'category',
                            data : numTime,
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
                            }
                        },
                        //辅助x轴
                        {
                            type : 'category',
                            axisLine: {show:false},
                            axisTick: {show:false},
                            axisLabel: {show:false},
                            splitArea: {show:false},
                            splitLine: {show:false},
                            data : ['s1','s2','s3','s4','s5']
                        }
                ],
                series: [{
                    name: numTit,
                    type: 'bar',
                    itemStyle: {
                        normal: {
                            color: colors[1]

                        }
                    },
                    barWidth : 5,//柱图宽度
                    data: numData
                } 
                //背景
                // ,{
                //     name:name,
                //     type: 'bar',
                //     silent: true,
                //     barGap: '-100%',  //柱间距离，默认为柱形宽度的30%，可设固定值
                //     xAxisIndex:1,  //当不指定时默认控制所有纵向类目，仅一个时可为数字
                //     data: Datam,
                //     //颜色需要有透明度
                //     itemStyle: {
                //         normal: {
                //             color: colors[5]

                //         }
                //     },
                //     barWidth : 5,//柱图宽度
                // }


                ]
            };

       //自动触发图表的tooltip行为
        numData.currentIndex = -1;
        setInterval(function () {
            var dataLen = numData.length;

            // 取消之前高亮的图形
            myChart.dispatchAction({
                type: 'downplay',
                seriesIndex: 0,
                dataIndex: numData.currentIndex
            });

            numData.currentIndex = (numData.currentIndex + 1) % dataLen;


            // 高亮当前图形
            myChart.dispatchAction({
                type: 'highlight',
                seriesIndex: 0,
                dataIndex: numData.currentIndex
            });
            // 显示 tooltip
            myChart.dispatchAction({
                type: 'showTip',
                seriesIndex: 0,
                dataIndex: numData.currentIndex
            });
        }, 1000);

           
    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);
}
