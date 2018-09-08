function scatter(object,titname,analyseData,YUnit){  ////对象,主标题,y轴单位
    var myChart = echarts.init(object);
    var colors=['#11e8e6','#ffb700','#002dbe','#ddd','#fff'];  //图表青，图表黄，基线（轴线）深蓝,轴数据#333，标题色        
    var fontSizes=['12','14','16'];      
    option = {
        title : {
            text: titname,
            x:'0%',   //水平安放位置
            y:'0%',
            textStyle:{
                color: colors[4],
                fontSize: fontSizes[1],
                fontWeight: 'normal'
            }
        },
        tooltip : {
            // trigger: 'axis',   //悬浮显示虚线
            showDelay : 0,
            formatter : function (params) {
                if (params.value.length > 1) {
                    return params.value[2]+' <br/>'+params.seriesName + ' :'
                       + params.value[0] + YUnit +','
                       + params.value[1] + YUnit;
                }
                else {
                    return params.seriesName + ' :<br/>'
                       + params.name + ' : '
                       + params.value + '万 ';
                }
            }, 
            axisPointer:{
                show: false,
                type : 'cross',
                lineStyle: {
                    type : 'dashed',
                    width : 1
                }
            }
        },
        grid: {          //图表整体位置
            left: '3%',
            right: '8%',
            top:'25%',
            bottom: '10%',
            containLabel: true
        },
        xAxis : [
            {
                type : 'value',
                scale:true,
                axisLabel : {
                    formatter: '{value}'+YUnit,
                    textStyle: {
                        color: colors[3]
                    },
                },
                axisLine: {               //基线颜色
                    lineStyle: {
                        color: colors[2]
                    }
                },
                splitLine:{show: false},//去除网格线
                splitNumber: 3,
            }
        ],
        yAxis : [
            {
                type : 'value',
                scale:true,
                axisLabel : {
                    formatter: '{value}'+YUnit,
                    textStyle: {
                        color: colors[3]
                    },
                },
                axisLine: {               //基线颜色
                    lineStyle: {
                        color: colors[2]
                    }
                },
                splitLine:{show: false},//去除网格线
                splitNumber: 1,

            }
        ],
        series : [
            {
                name:titname,
                type:'scatter',
                label: {
                    normal: {
                        show: true,
                        formatter: function(param) {  //
                            return param.data[2];
                        },
                        position: 'top',
                        textStyle:{
                            color:colors[0],
                        }
                    },
                },
                itemStyle: {
                        normal : {
                            color:colors[0],
                        }
                    },
                data: analyseData
            },
           
        ]
    }; 

    //自动触发图表的tooltip行为
        analyseData.currentIndex = -1;
        setInterval(function () {
            var dataLen = analyseData.length;

            // 取消之前高亮的图形
            myChart.dispatchAction({
                type: 'downplay',
                seriesIndex: 0,
                dataIndex: analyseData.currentIndex
            });

            analyseData.currentIndex = (analyseData.currentIndex + 1) % dataLen;


            // 高亮当前图形
            myChart.dispatchAction({
                type: 'highlight',
                seriesIndex: 0,
                dataIndex: analyseData.currentIndex
            });
            // 显示 tooltip
            myChart.dispatchAction({
                type: 'showTip',
                seriesIndex: 0,
                dataIndex: analyseData.currentIndex
            });
        }, 1000);     
    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option); 
}