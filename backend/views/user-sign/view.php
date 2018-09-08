<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\UserSign */

$this->title = $model->userOne->username??'';
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'User Signs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-sign-view">
	<?php 
	if(empty($model))
	{
	    echo '<div>查询内容不符合级别</div>';
	}else{
	?>
    <h1><?= Html::encode($this->title) ?></h1>
    <table id="w0" class="table table-striped table-bordered detail-view">
        <tr>
            <td>用户名</td>
            <td><?php echo $model->userOne->username;?></td>
        </tr>
        <tr>
            <td>姓名</td>
            <td><?php echo $model->userOne->name;?></td>
        </tr>
        <tr>
            <td>考勤情况</td>
            <td><?php if($model->source_type==1){echo'云管理';}else{echo '考勤机';}?></td>
        </tr>
        <tr>
            <td>考勤地址</td>
            <td><?php echo $model->path;?></td>
        </tr>
        <tr>
            <td>考勤经度</td>
            <td><?php echo $model->longitude;?></td>
        </tr>
        <tr>
            <td>考勤纬度</td>
            <td><?php echo $model->latitude;?></td>
        </tr>
        <tr>
            <td>备注</td>
            <td><?php echo $model->remarks;?></td>
        </tr>
        <tr>
            <td>时间</td>
            <td><?php echo date('Y-m-d H:i:s',$model->time);?></td>
        </tr>
        <tr>
            <td>状态</td>
            <td><?php 
            if($model->is_late==0 )
            {
                echo '正常';
            }
            if($model->is_late==1)
            {
                echo '迟到'.$model->is_late_time.'分';
            }
            if($model->is_late==2)
            {
                echo '早退'. $model->is_late_time.'分';
            }
            ?></td>
        </tr>
        <tr>
            <td>照片</td>
            <td><img src="<?php echo 'http://dev.crm.openapi.xunmall.com/'.$model->image;?>" onclick="this.width+=50;this.height+=50" onclick="javascript:window.open(this.src);"/></td>
        </tr>
    </table>
	<?php }?>
</div>
