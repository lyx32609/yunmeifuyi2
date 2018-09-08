/* 柱形-折现图 */
function barLine(object,titname,dataxAxis,bardata,linedata,barname,linename,YUnit){  //对象 x轴类目，柱形数组，折线数组，柱形名，折线名，y轴单位
    var myChart = echarts.init(object);
    // 指定图表的配置项和数据 
    var colors=['#11e8e6','#ffb700','#002dbe','#ddd','#fff'];  //图表青，图表黄，基线（轴线）深蓝,轴数据#333，标题色
    var fontSizes=['12','14','16'];
    option = {
        color:colors,
        fontSize:fontSizes,
        "title": {
            text: titname,
            x:'0%',   //水平安放位置
            y:'0%',
            textStyle: {
                color: colors[4],
                fontSize: fontSizes[1],
                fontWeight: 'normal',
            }
        },
        "tooltip": {
            "trigger": "axis",
            "axisPointer": {
                "type": "none",
                textStyle: {
                    color: "#fff"
                }

            },
        },
        "grid": {
            'left': '3%',
            'right': '4%',
            'top':'25%',
            'bottom': '5%',
            'textStyle': {
                'color': "#fff"
            },
            'containLabel': true
        },
        "legend": {   //图例
            x: '25%',
            top: '3%',
            textStyle: {
                color: '#90979c',
            },
            "data": [barname,linename]   //图例内容数组，数组项通常为{string}，每一项代表一个系列的name，默认布局到达边缘会自动分行（列），传入空字符串''可实现手动分行（列）
        },
        "xAxis": [{
            "type": "category",
            "axisLine": {
               'lineStyle': {
                    color: colors[2]
                }
            },
            "splitLine": {
                'lineStyle': {
                    color: colors[2]
                }
            },
            "axisTick": {
                "show": false
            },
            "splitArea": {
                "show": false
            },
            "axisLabel": {
                
                 'textStyle': {
                    'color':colors[3]
                },
                'formatter': '{value}',  //添加y轴单位

            },
            "data": dataxAxis,
            "splitNumber": 2
            
        }],
        "yAxis": [{
            "type": "value",
            "splitLine": {
                "show": false
            },
            "axisLine": {    //坐标轴名称文字样式，默认取全局配置，颜色跟随axisLine主色，可设
                'lineStyle': {
                    color: colors[2]
                }
            },
            "axisTick": {
                "show": false
            },
            "axisLabel": {
                // "interval": 0,
                'textStyle': {
                    'color': colors[3]
                },
                'formatter': '{value}'+YUnit  //添加y轴单位

            },
            "splitArea": {
                "show": false
            },
            "splitNumber": 2

        }],
        "series": [{
                "name": barname,
                "type": "bar",
                "stack": "总量",
                "barMaxWidth": 35,
                "barGap": "10%",
                "itemStyle": {
                    "normal": {
                         //设置渐变颜色
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                offset: 0,
                                // color: 'rgba(17, 168,171, 1)'
                                color: colors[0]
                            }, {
                                offset: 1,
                                color: 'rgba(76,169,235, 0.3)'
                            }]),
                        "label": {   //显示图表数据数值
                            "show": false,   
                            "textStyle": {
                                "color": "#fff"
                            },
                            "position": "insideTop",
                            formatter: function(p) {
                                return p.value > 0 ? (p.value) : '';
                            }
                        }
                    }
                },

                "data":bardata
            },
            {
                "name":linename,
                "type": "line",
                "stack": "总量",
                symbolSize:10,
                symbol:'circle',
                "itemStyle": {
                    "normal": {
                        "color": colors[1],
                        "barBorderRadius": 0,
                        "label": {
                            "show": false,
                            "position": "top",
                            formatter: function(p) {
                                return p.value > 0 ? (p.value) : '';
                            }
                        }
                    }
                },
                "data": linedata
            },
        ]
    };   

    //自动触发图表的tooltip行为
        linedata.currentIndex = -1;
        setInterval(function () {
            var dataLen = linedata.length;

            // 取消之前高亮的图形
            myChart.dispatchAction({
                type: 'downplay',
                seriesIndex: 0,
                dataIndex: linedata.currentIndex
            });

            linedata.currentIndex = (linedata.currentIndex + 1) % dataLen;


            // 高亮当前图形
            myChart.dispatchAction({
                type: 'highlight',
                seriesIndex: 0,
                dataIndex: linedata.currentIndex
            });
            // 显示 tooltip
            myChart.dispatchAction({
                type: 'showTip',
                seriesIndex: 0,
                dataIndex: linedata.currentIndex
            });
        }, 1000);
    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option); 
}