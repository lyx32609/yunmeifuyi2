//日期
$("#date_start").prop("readonly", true).datetimepicker({
    timeText: '时间',
    hourText: '小时',
    minuteText: '分钟',
    secondText: '秒',
    currentText: '现在',
    closeText: '完成',
    showSecond: true, //显示秒
    timeFormat: 'HH:mm:ss', //格式化时间
    onClose: function(){
        $("#date_start").val($(this).val());
    }
});

$("#date_end").prop("readonly", true).datetimepicker({
    timeText: '时间',
    hourText: '小时',
    minuteText: '分钟',
    secondText: '秒',
    currentText: '现在',
    closeText: '完成',
    showSecond: true, //显示秒
    timeFormat: 'HH:mm:ss', //格式化时间
    onClose: function(){
        var start=$("#date_start").val();
        var end=$("#date_end").val();
        //js日期格式转换成时间戳
        var startDate = new Date(start);
        var startTime = Date.parse(startDate);
        var endDate = new Date(end);
        var endTime = Date.parse(endDate);
        console.log(startTime,endTime);
        if(endTime<=startTime){
            alert("结束时间早于开始时间，请重新选择");
            $("#date_end").val("");
        }
    }
});