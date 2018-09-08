<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="/css/petition3.css" rel="stylesheet" type="text/css">
    <style>
        col{
            width:21px;
        }
        col:nth-of-type(1),
        col:nth-of-type(2),
        col:nth-of-type(7){
            width:24px;
        }
        col:nth-of-type(3){
            width:29px;
        }
    </style>
</head>

<body>
<?php 
        $detail = $data['detail'];
        $message = json_decode($data['detail']['message']);
        $list1 = $data['list1'];//加签
        $list = $data['list'];//审批

    ?>
<div class="wrap">
    <div class="content">
        <div class="tit">
            <h3>山东云媒软件股份有限公司签呈</h3>
        </div>
        <div class="cont">
        <table class="tg" style="table-layout:fixed;">
            <tr>
                <td class="tg-s6z2" colspan="3">分类</td>
                <td class="tg-s6z2" colspan="13" name="typePetition">付款</td>
                <td class="tg-s6z2" colspan="3">提报日期</td>
                <td class="tg-s6z2" colspan="11" name="create_time"><?php echo $detail['create_time']?></td>
            </tr>
            <tr>
                <td class="tg-s6z2" colspan="3">用途</td>
                <td class="tg-yw4l" colspan="27" name="purpose"><?php echo $message->purpose;?></td>
            </tr>
            <tr>
                <td class="tg-s6z2" colspan="3">提报部门</td>
                <td class="tg-s6z2" colspan="13" name="domain"><?php echo $detail['domain']?></td>
                <td class="tg-s6z2" colspan="3">提报人</td>
                <td class="tg-s6z2" colspan="11" name="domainStaff"><?php echo $detail['name']?></td>
            </tr>
            <tr>
                <td class="tg-s6z2" colspan="3">付款方式</td>
                <td class="tg-s6z2" colspan="4" name="mode"><?php switch($message->mode){case 1:echo '现金';break;case 2:echo "转账";break;case 3:echo "其他";break;}?></td>
                <td class="tg-s6z2" colspan="4">收款人全称</td>
                <td class="tg-s6z2" colspan="5" name="name"><?php echo $message->name;?></td>
                <td class="tg-s6z2" colspan="3">金额</td>
                <td class="tg-s6z2" colspan="11" name="money"><?php echo $message->money;?></td>
            </tr>
            <tr>
                <td class="tg-s6z2" colspan="3">收款账号</td>
                <td class="tg-s6z2" colspan="13" name="account"><?php echo $message->account;?></td>
                <td class="tg-s6z2" colspan="3">开户银行</td>
                <td class="tg-s6z2" colspan="11" name="accountbank"><?php echo $message->accountbank;?></td>
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
                <img class="img_box" src="<?php if($data['detail']['source'] == 1){echo $v;}else{echo Yii::$app->params['domain_cpi'].$v;}?>"/>
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
        </div>
    </div>
</div>
<script src="/static/js/imgPreview.js"></script>
</body>
</html>