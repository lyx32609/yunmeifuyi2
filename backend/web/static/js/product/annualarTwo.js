function annularTwo(object,titName,currentYear,dataCurrent,dataIncrease,className){  //对象 标题名，当年年份，当年数据，增长数据，类目名
    var myChart = echarts.init(object);
        // 指定图表的配置项和数据
        var colors=['#f6cb26','#014de1','#8a0bbd','#ddd','#fff',/*'#108ac6'*/];  //环图表黄，环图表蓝，环图表紫,类目数据色 标题色
        var fontSizes=['12','14','16'];
        option = {
            color:colors,
            title:  {
                show:false,
                text: titName,
                left: '50%',
                bottom: '0%',
                textAlign: 'center',
                textStyle:{
                    color: colors[4],
                    fontSize: fontSizes[0],
                    fontWeight: 'normal'
                },
            }, 
             tooltip: {
                trigger: 'item',
                formatter: "{a} <br/>{b}: {c} ({d}%)"  //图表名 类目名  数值 数值百分比
            },           
            series: [
            {
                name:className,
                type: 'pie',
                radius: ['90%', '60%'],  //外环，内环
                center: ['50%', '50%'],  //圆心坐标
                label: {
                    normal: {
                        position: 'center'
                    }
                },
                data: [{
                    value: dataCurrent,
                    name: currentYear,
                    itemStyle: {
                        normal: {
                            color: colors[0]
                        }
                    },
                    label: {
                        normal: {
                            formatter: '{d} %',  //环形中间显示的百分比
                            textStyle: {
                                color: colors[3],
                                fontSize: fontSizes[0]

                            }
                        }
                    }
                }, {
                    value: dataIncrease,
                    name: '增长',
                    tooltip: {
                        show: false
                    },
                    itemStyle: {
                        normal: {
                            color: colors[1],
                        }
                    },
                    label: {
                        normal: {
                            textStyle: {
                                color: colors[3],
                            },
                            // formatter: '\n手机号注册'
                        }
                    }
                }]
            }]
        };
    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);
}