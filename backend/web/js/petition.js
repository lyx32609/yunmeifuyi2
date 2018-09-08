//签呈类别
function classPeition(value) {
    var type;
    switch (value) {
        case "0":
            type = "通用";
            break;
        case "1":
            type = "领用";
            break;
        case "2":
            type = "用车";
            break;
        case "3":
            type = "付款";
            break;
        case "4":
            type = "报销";
            break;
        case "5":
            type = "采购";
            break;
        case "6":
            type = "用证";
            break;
        case "7":
            type = "用印";
            break;
        case "8":
            type = "出差";
            break;
        case "9":
            type = "加班";
            break;
        case "10":
            type = "外出";
            break;
        case "11":
            type = "转正";
            break;
        case "12":
            type = "离职";
            break;
        case "13":
            type = "请假";
            break;
        case "14":
            type = "招聘";
            break;
    }
    $('.tg [name="typePetition"]').html(type);
}

//多加签审批人
function approverAdd(moreApprover) {
    var cont = '';
    for (var i = 0; i < moreApprover.length; i++) {
        cont += "<tr><td  class='tg-s6z2' rowspan='2' colspan='3'>审批人（加签）</td><td class='tg-s6z2' colspan='3'>部门</td><td class='tg-s6z2' colspan='6'>" + moreApprover[i].domain + "</td><td class='tg-s6z2' colspan='3'>姓名</td><td class='tg-s6z2' colspan='6'>" + moreApprover[i].name + "</td><td class='tg-s6z2' colspan='3'>日期</td><td class='tg-s6z2' colspan='6'>" + moreApprover[i].add_time + "</td></tr><tr><td class='tg-031e' colspan='27'><i>加签意见：</i><span>" + moreApprover[i].add_advice + "</span></td></tr>";
    }
    $(".content table").append(cont);
}
//多审批、转签审批人
function approver(moreApprover) {

    var cont = '';
    for (var i = 0; i < moreApprover.length; i++) {
        //console.log(moreApprover[i].status);
        var appPerson = moreApprover[i].tag == 2 ? "审批人" + "<br/>" + "（转签）" : "审批人";
        var advice = (moreApprover[i].status == 0 || moreApprover[i].status == 2 || moreApprover[i].status == 5) ? moreApprover[i].advice : "电子签呈审核已同意" + "<br/>" + moreApprover[i].advice;
        cont += "<tr><td  class='tg-s6z2' rowspan='2' colspan='3'>" + appPerson + "</td><td class='tg-s6z2' colspan='3'>部门</td><td class='tg-s6z2' colspan='6'>" + moreApprover[i].domain + "</td><td class='tg-s6z2' colspan='3'>姓名</td><td class='tg-s6z2' colspan='6'>" + moreApprover[i].name + "</td><td class='tg-s6z2' colspan='3'>日期</td><td class='tg-s6z2' colspan='6'>" + moreApprover[i].examine_time + "</td></tr><tr><td class='tg-031e' colspan='27'><i>审批意见：</i><span class='advice'>" + advice + "</span></td></tr>";
    }
    $(".content table").append(cont);

}

//类型--多选
function checkedMore(object, value) {
    var dutyArr = new Array();
    dutyArr = value.split("");
    for (var i = 0; i < dutyArr.length; i++) {   //优先遍历用户多选数据，遍历次数相对少
        for (var j = 0; j < object.length; j++) {
            var explainVal = object.eq(j).val();
            if (explainVal == dutyArr[i]) {
                object.eq(j).attr("checked", true);
                object.siblings("img").eq(j).attr("src", "/images/checked.png");
                //$(".explain label").eq(j).css("display",'block');
            }
        }
    }

}
//分页代码--未解决分隔问题

//截取table
// function pageResult() {
//     var trHeightAll = new Array();//每一行tr的高度
//     var pageArr = {};//每一个table所存数据

//     //遍历tr高度，求总高度
//     var sum = 0;
//     for (var i = 0; i < $(".cont table tr").length; i++) {
//         trHeightAll.push($(".cont table tr").eq(i).height());
//     }
//     for (var n = 0; n < trHeightAll.length; n++) {
//         sum += trHeightAll[n];
//         setPage(pageArr, sum,n);
//     }

//     for(var item in pageArr){
//         //console.log(pageArr[item]);
//         newTable(pageArr[item]);
//     }

//     //console.log(trHeightAll);
//     //console.log(pageArr);
// }

// //设置分页
// function setPage(arr, nHeight, index) {
//     var num = parseInt(nHeight / 1250);
//     var tnum = num;
//     if(((num + 1) * 1250 - nHeight) <50){
//         tnum = tnum + 1;
//     }
//     if (!arr['page' + tnum]) {
//         arr['page' + tnum] = new Array();
//     }
//     arr['page' + tnum].push(index);

// }

// //生成新的table
// function newTable(count) {
//     //把tr放入新的table
//     var trCont = '';
//     var trHeight1 = new Array();
//     var allheight=0;
//     for (var m = count[0]; m <= count[count.length - 1]; m++) {
//         // console.log(count[0], count[count.length - 1]);
//         trCont += $('.cont tr').eq(m).prop('outerHTML');
//         trHeight1.push($('.cont tr').eq(m).height());
//     }
//     for(var l=0;l<trHeight1.length;l++){
//         allheight+=trHeight1[l];
//     }
//     console.log(allheight);

//     var tableNew = "<div class='divHeight' style='width:100%;height:1247px;background: #fff;'><table class='tg tabHeight' style='table-layout:fixed;margin:0px auto;'>" + trCont + "</table></div>";
//     //var tableNew = "<div class='divHeight' style='width:100%;height:1247px;background: #fff;' class='pfpf'><table class='tg tabHeight' style='table-layout:fixed;margin:0px auto;'>" + trCont + "</table></div>";

//     if (count.length != 0) {
//         $(".cont").append(tableNew);
//     } else {
//         $(".cont").append("");
//     }

// }

// //转pdf
// function pdf(count){
//     var downPdf = count;
//     downPdf.onclick = function() {
//         html2canvas(document.body, {
//             onrendered:function(canvas) {

//                 var contentWidth = canvas.width;
//                 var contentHeight = canvas.height;

//                 //一页pdf显示html页面生成的canvas高度;
//                 var pageHeight = contentWidth / 595.28 * 841.89;
//                 //未生成pdf的html页面高度
//                 var leftHeight = contentHeight;
//                 //pdf页面偏移
//                 var position = 0;
//                 //a4纸的尺寸[595.28,841.89]，html页面生成的canvas在pdf中图片的宽高
//                 var imgWidth = 555.28;
//                 var imgHeight = 555.28/contentWidth * contentHeight;

//                 var pageData = canvas.toDataURL('image/jpeg', 1.0);

//                 //console.log(pageData);

//                 var pdf = new jsPDF('', 'pt', 'a4');
//                 //有两个高度需要区分，一个是html页面的实际高度，和生成pdf的页面高度(841.89)
//                 //当内容未超过pdf一页显示的范围，无需分页
//                 if (leftHeight < pageHeight) {
//                     pdf.addImage(pageData, 'JPEG', 20, 0, imgWidth, imgHeight );
//                 } else {
//                     while(leftHeight > 0) {
//                         pdf.addImage(pageData, 'JPEG', 20, position, imgWidth, imgHeight)
//                         leftHeight -= pageHeight;
//                         position -= 841.89;
//                         //避免添加空白页
//                         if(leftHeight > 0) {
//                             pdf.addPage();
//                         }
//                     }
//                 }
//                 pdf.save('content.pdf');
//                 $("#download").attr('href', pageData).get(0).click();
//             },
//             background: "#ffffff",
//         })
//     }
// }




