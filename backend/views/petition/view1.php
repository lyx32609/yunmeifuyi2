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
        $detail = ['detail'];
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
                <td class="tg-s6z2" colspan="13" name="typePetition">通用</td>
                <td class="tg-s6z2" colspan="3">提报日期</td>
                <td class="tg-s6z2" colspan="11" name="create_time"><?php echo $data['detail']['create_time']?></td>
            </tr>
            <tr>
                <td class="tg-s6z2" colspan="3">提报部门</td>
                <td class="tg-s6z2" colspan="13" name="domain"><?php echo $data['detail']['domain']?></td>
                <td class="tg-s6z2" colspan="3">提报人</td>
                <td class="tg-s6z2" colspan="11" name="domainStaff"><?php echo $data['detail']['name']?></td>
            </tr>
            <tr>
                <td class="tg-s6z2" colspan="3">内容</td>
                <td class="tg-031e" colspan="27" name="content"><?php echo $message;?></td>
            </tr>
            <?php if($list1){ foreach($list1 as $v){?>
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
            <?php if($list){ foreach($list as $v){?>
                <tr>
                    <td  class='tg-s6z2' rowspan='2' colspan='3'><?php if($v['tag'] == 2){echo "审批人<br/>（转签）";}else{echo '审批人';}?></td>
                    <td class='tg-s6z2' colspan='3'>部门</td>
                    <td class='tg-s6z2' colspan='6'><?php echo $v['domain']; ?></td>
                    <td class='tg-s6z2' colspan='3'>姓名</td>
                    <td class='tg-s6z2' colspan='6'><?php echo $v['name']; ?></td>
                    <td class='tg-s6z2' colspan='3'>日期</td>
                    <td class='tg-s6z2' colspan='6'><?php echo $v['examine_time']; ?></td>
                </tr>
                <tr>
                    <td class='tg-031e' colspan='27'><i>审批意见：</i><span class='advice'><?php echo $v['advice']; ?></span>
                    </td>
                </tr>
            <?php }}?>
        </table>
        </div>
    </div>
</div>
</body>
</html>