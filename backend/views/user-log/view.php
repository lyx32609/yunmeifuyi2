<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\User;

/* @var $this yii\web\View */
/* @var $model backend\models\UserLog */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '操作日志', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-log-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php 
    $user = User::findOne($model->user_id);
    // if($user["username"]  == "160044")
    // {
    //     $user["name"] = "超级管理员";
    // }
        switch($model->type)
        {
            case 1: $type = "修改店铺定位坐标";
            case 2: $type = "登录/退出后台";
            case 3: $type = "用户管理模块";
        }
    ?>
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
            "label"=>"用户名",
            'attribute' => 'user_id',
            "value"=>$user["username"]
            ],
            [
            "label"=>"姓名",
            'attribute' => 'user_id',
            "value"=>$user["name"]
            ],
            [
            "label"=>"操作类型",
            'attribute' => 'type',
            "value"=>$type
            ],
            [
            "label"=>"操作内容",
            'attribute' => 'log_title',
            "value"=>$model->log_title
            ],
            [
            "label"=>"操作详情",
            'attribute' => 'log_text',
            "value"=>$model->log_text
            ],
            [
            "label"=>"操作时间",
            'attribute' => 'ntext',
            "value"=>date("Y-m-d H:m:s",$model->add_time)
            ],
        ],
    ]) ?>

</div>
