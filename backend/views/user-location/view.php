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
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label'=>'用户',
                'value'=>isset($model->userOne->usernmae)&&!empty($model->userOne->usernmae)?$model->userOne->usernmae:'用户名丢失',
            ],
            [
                'label'=>'签到情况',
                'value'=>$model->type==1?'签到':'签退',
            ],
            [
            'label'=>'时间',
            'value'=>date('Y-m-d H:i:s',$model->time),
            ],
            'longitude',
            'latitude',
            [
                'label'=>'上传图像',
                'format'=>'image',
                'value'=>$model->image,
            ],

        ],
    ]) ?>
	<?php }?>
</div>
