function showBar(object,xData,yDataOne,yDataTwo){
    //- BAR CHART ----获取柱状图数据
    var myChart = echarts.init(object);
    var colors=['#25ca3d','#1650f1','#3796ff','#333333','#d8dce5']; //绿色 渐变蓝1 渐变蓝2 字体黑 轴线灰
    option = {
        grid: {
            top:'5%',
            left: '0%',
            right: '4%',
            bottom: '5%',
            containLabel: true
        },
        tooltip : {
             trigger: 'axis',
             axisPointer: {
                type: 'shadow',  //去掉柱形悬浮中间出现的默认直线
            },
            triggerOn:'click',   //点击事件
            formatter: function (params) {
                //console.log(params);
                var nameArr=params[0].axisValueLabel.split("   ");
                var res ="<span style='color:"+colors[0]+"'>\u25CF</span>"+" "+ nameArr[0]+'：'+params[0].data+'<br/>'+"<span style='color:"+colors[1]+"'>\u25CF</span>"+" "+nameArr[1]+'：'+params[1].data;
                return res;
            }
        },
        xAxis: {
            //设置坐标轴字体颜色和宽度
            axisLabel: {
                show: true,
                textStyle: {
                    color: colors[3]
                }
            },
            axisLine: {
                lineStyle: {
                    color: colors[4],
                    fontSize:'12',
                }
            },
            type: 'category',
            data: xData,
        },
        yAxis: {
//                scale:true,
            type: 'value',
            splitNumber: 4,//Y轴分段段数
            minInterval : 1,
            boundaryGap: [0,0.01], //坐标轴空白策略
            axisTick: {  //取消刻度线
                show: false
            },
            //设置y轴分隔线颜色
            splitLine: {
                show: true,
                lineStyle: {
                    color: colors[4],
                    width: 1
                }
            },
            axisLabel: {
                show: true,
                textStyle: {
                    color: colors[3],
                    fontSize:'12',
                },

            },
            min:0,
            //设置y轴颜色
            axisLine: {
                lineStyle: {
                    color: '#fff'
                }
            },
        },
        series: [
            {
                barMinHeight:1,
                name: '2011年',
                type: 'bar',
                data: yDataOne,
                barWidth : 25,//柱图宽度
                itemStyle: {
                    normal: {
                        color:colors[0],
                        barBorderRadius:[12.5, 12.5, 0, 0],//（顺时针左上，右上，右下，左下)
                        borderWidth: 0,

                    }
                },
                labelLine: {
                    show: true
                },
            },
            {
                barMinHeight:1, //设置最小值，数据为0显示
                name: '2012年',
                type: 'bar',
                barWidth : 25,//柱图宽度
                data:yDataTwo,
                itemStyle: {
                    normal: {
                        show: true,
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                            offset: 0,
                            color: colors[1]
                        }, {
                            offset: 1,
                            color: colors[2]
                        }]),
                        barBorderRadius:[12.5, 12.5, 0, 0],//（顺时针左上，右上，右下，左下)
                        borderWidth: 0,
                    }
                },
            }
        ]
    };
    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);
}