<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>招聘</title>
    <link href="/css/petition3.css" rel="stylesheet" type="text/css">
</head>

<body>
            <?php
            // echo "<pre>";
            // print_r($data);
            // exit();
        $detail = $data['detail'];
        $message = json_decode($data['detail']['message']);
        // echo "<pre>";
        // print_r($message);
        // exit();
        $list1 = $data['list1'];//加签
        $list = $data['list'];//审批
    ?>
<div class="wrap">
    <div class="content">
        <div class="tit">
            <h3>山东云媒软件股份有限公司签呈</h3>
        </div>
        <div class="cont">
            <table class="tg" style="table-layout: fixed;" id='example'>
                <colgroup>
                    <col style="width: 8%">
                    <col style="width: 8%">
                    <col style="width: 9.6%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 24px">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                    <col style="width: 7%">
                </colgroup>
                <tr>
                    <td class="tg-s6z2" colspan="3">分类</td>
                    <td class="tg-s6z2" colspan="13" name="typePetition">招聘</td>
                    <td class="tg-s6z2" colspan="3">提报日期</td>
                    <td class="tg-s6z2" colspan="11" name="create_time"><?php echo $detail['create_time']?></td>
                </tr>
            <tr>
                <td class="tg-s6z2" colspan="3">提报部门</td>
                <td class="tg-s6z2" colspan="13" name="domain"><?php echo $detail['domain']?></td>
                <td class="tg-s6z2" colspan="3">提报人</td>
                <td class="tg-s6z2" colspan="11" name="domainStaff"><?php echo $detail['name']?></td>
            </tr>
                <tr>
                    <td class="tg-baqh" colspan="3">需求岗位或职位名称</td>
                    <td class="tg-s6z2" colspan="13" name="dutyname"><?php echo $message->dutyname;?></td>
                    <td class="tg-s6z2" colspan="3">需求名额</td>
                    <td class="tg-s6z2" colspan="3" name="dutynum"><?php echo $message->dutynum;?></td>
                    <td class="tg-s6z2" colspan="4">人员到岗时间</td>
                    <td class="tg-s6z2" colspan="4" name="stationtime"><?php echo $message->stationtime;?></td>
                </tr>
                <tr>
                    <td class="tg-baqh" colspan="3">拟定试用期岗位名称</td>
                    <td class="tg-s6z2" colspan="13" name="plandutyname"><?php echo $message->plandutyname;?></td>
                    <td class="tg-s6z2" colspan="3">拟定待遇</td>
                    <td class="tg-031e" colspan="11">拟定试用期：<em name="planprobation"><?php echo $message->planprobation;?></em><br>拟定转正后：<em name="planregularworker"><?php echo $message->planregularworker;?></em></td>
                </tr>
                <tr>
                    <td class="tg-s6z2">人员需求理由说明</td>
                    <td class="tg-yw4l explain" colspan="29">
                        <input type="hidden"  value="0"><img src="<?php if(stristr($message->dutyreasonexplain,'0') != false){?>/images/checked.png<?php }else{?>/images/checkedNo.png<?php }?>"/><label for="" >扩大编制</label>
                        <input type="hidden"  value="1"><img src="<?php if(stristr($message->dutyreasonexplain,'1') != false){?>/images/checked.png<?php }else{?>/images/checkedNo.png<?php }?>"/><label for="" >业务扩充</label>
                        <input type="hidden"  value="2"><img src="<?php if(stristr($message->dutyreasonexplain,'2') != false){?>/images/checked.png<?php }else{?>/images/checkedNo.png<?php }?>"/><label for="" >补充离职或调动</label>
                        <input type="hidden"  value="3"><img src="<?php if(stristr($message->dutyreasonexplain,'3') != false){?>/images/checked.png<?php }else{?>/images/checkedNo.png<?php }?>"/><label for="">储备人力</label>
                        <input type="hidden"  value="4"><img src="<?php if(stristr($message->dutyreasonexplain,'4') != false){?>/images/checked.png<?php }else{?>/images/checkedNo.png<?php }?>"/><label for="">短期需要</label>
                        <p class="w_97">
                            <i>详细说明：</i><span  name="detailexplain"><?php echo $message->detailexplain;?>
                        </p>

                        <p class="f_right mr_120">需求部门签字：</p>

                    </td>
                </tr>
                <tr>
                    <td class="tg-s6z2" rowspan="2">需求资格</td>
                    <td class="tg-s6z2">性别</td>
                    <td class="tg-s6z2" colspan="4" name="sex"><?php switch($message->sex){
                        case 0:echo '男';
                        break;
                        case 1:echo '女';
                        break;
                        case 2:echo '不限';
                        break;

                    };?></td>
                    <td class="tg-s6z2">年龄</td>
                    <td class="tg-s6z2" colspan="2" name="age"><?php echo $message->age;?></td>
                    <td class="tg-s6z2" colspan="2">教育程度</td>
                    <td class="tg-s6z2" colspan="10" name="education"><?php 
                    switch($message->education){
                        case 0:echo '中专';
                        break;
                        case 1:echo '大专';
                        break;
                        case 2:echo '本科';
                        break;
                        case 3:echo '硕士';
                        break;
                        case 4:echo '博士';
                        break;
                        case 5:echo '其他';
                        break;
                    }?></td>
                    <td class="tg-s6z2" colspan="2">内调外招</td>
                    <td class="tg-s6z2 in_out" colspan="7">
                        <img  class="inside" src="<?php if(($message->adjust == 0) || ($message->adjust == 2)){?>/images/checked.png<?php }else{?>/images/checkedNo.png<?php }?>"/><label for="" >内调</label>
                        <img  class="outside" src="<?php if(($message->adjust == 1) || ($message->adjust == 2)){?>/images/checked.png<?php }else{?>/images/checkedNo.png<?php }?>"/><label for="" >外招</label>
                    </td>
                </tr>
                <tr>
                    <td class="tg-s6z2">专业</td>
                    <td class="tg-s6z2" colspan="4" name="major"><?php echo $message->major;?></td>
                    <td class="tg-s6z2">经验</td>
                    <td class="tg-031e" colspan="14" name="experience"><?php echo $message->experience;?></td>
                    <td class="tg-031e" colspan="9">经验年限：<em name="experienceyear"><?php echo $message->experienceyear;?></em></td>
                </tr>
                <tr class="three">
                    <td class="tg-s6z2" rowspan="3">招聘岗位工作说明书</td>
                    <td class="tg-s6z2">岗位职责</td>
                    <td class="tg-yw4l" colspan="28" name="stationresponsibility">
                        <?php echo $message->stationresponsibility;?>
                    </td>
                </tr>
                <tr>
                    <td class="tg-s6z2">任职要求</td>
                    <td class="tg-yw4l" colspan="28" name="officerequire">
                        <?php echo $message->officerequire;?>
                    </td>
                </tr>
                <tr>
                    <td class="tg-s6z2">其他要求</td>
                    <td class="tg-yw4l" colspan="28" name="elserequire">
                        <?php echo $message->elserequire;?>
                    </td>
                </tr>
                 <?php if(!empty($list1)){foreach($list1 as $v){?>
                <tr><td  class='tg-s6z2' rowspan='2' colspan='3'>审批人（加签）</td>
                    <td class='tg-s6z2' colspan='3'>部门</td>
                    <td class='tg-s6z2' colspan='6'><?php echo $v['domain']; ?></td>
                    <td class='tg-s6z2' colspan='3'>姓名</td>
                    <td class='tg-s6z2' colspan='6'><?php echo $v['name']; ?></td>
                    <td class='tg-s6z2' colspan='3'>日期</td>
                    <td class='tg-s6z2' colspan='6'><?php echo $v['add_time']; ?></td>
                </tr>
                <tr>
                    <td class='tg-031e' colspan='27'><i>加签意见：</i><span><?php echo $v['add_advice']; ?></span></td>
                </tr>

            <?php }}?>
            <?php if(!empty($list)){ foreach($list as $v){?>
                <tr>
                    <td  class='tg-s6z2' rowspan='2' colspan='3'><?php if($v['tag'] == 2){echo "审批人<br/>（转签）";}else{echo '审批人';}?></td>
                    <td class='tg-s6z2' colspan='3'>部门</td>
                    <td class='tg-s6z2' colspan='6'><?php echo $v['domain']; ?></td>
                    <td class='tg-s6z2' colspan='3'>姓名</td>
                    <td class='tg-s6z2' colspan='6'><?php echo $v['name']; ?></td>
                    <td class='tg-s6z2' colspan='3'>日期</td>
                    <td class='tg-s6z2' colspan='3'><?php echo $v['examine_time']; ?></td>
                    <td class='tg-s6z2' colspan='3'>
                        <?php switch($v['status']){
                            case 0:echo "不同意";
                            break;
                            case 1:echo "同意";
                            break;
                            case 2:echo "待审";
                            break;
                            case 3:echo "同意已支付";
                            break;
                            case 4:echo "同意未支付";
                            break;
                            case 5:echo "已转签";
                            break;
                        }
                     ?></td>
                </tr>
                <tr>
                    <td class='tg-031e' colspan='27'><i>审批意见：</i><span class='advice'><?php echo $v['advice']; ?></span>
                    </td>
                </tr>
            <?php }}?>
                <tr><td colspan="3" align="center">图片</td><td colspan="27" style="padding-left:11px;">
                <?php
            $img = explode(",",$data['detail']['master_img']);
            foreach($img as $v){?>
                    <img class="img_box"  src="<?php if($data['detail']['source'] == 1){echo $v;}else{echo Yii::$app->params['domain_cpi'].$v;}?>"/>
                    <?php }
        $file = explode(",",$data['detail']['file']);?>
            </td>
            </tr>
            <tr>
                <td colspan="3" align="center">附件</td><td colspan="27"  class="file_box">
                <?php  foreach($file as $k=>$v){?>

                <a href="<?php if($data['detail']['source'] == 1){echo $v;}else{echo Yii::$app->params['domain_cpi'].$v;}?>">附件<?php echo $k+1?></a>
                <?php   }?>
            </td>
            </tr>
        </table>
        <div class="imgCopy"></div>
        <!-- <button id='btn' class='change btn btn-info'>导出为excel</button> -->
    </div>
</div>
</body>

</html>
<script src="/static/js/imgPreview.js"></script>
<script src="/js/table2excel.js"></script>
<script>
    $(function(){
        $("#btn").click(function(){
            $("#example").table2excel({
                // 不被导出的表格行的CSS class类
                exclude: ".noExl",
                // 导出的Excel文档的名称，（没看到作用）
                name: "招聘签呈",
                // Excel文件的名称
                filename: "招聘签呈"
            });
        });
    });
</script>