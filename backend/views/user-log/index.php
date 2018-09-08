<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = "操作日志";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-log-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]);?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
            "label"=>"用户名",
            'attribute' => 'user_id',
            "value"=>function($model){
                $user = User::findOne($model->user_id);
                return $user["username"];}
            ],
            [
            "label"=>"姓名",
            'attribute' => 'user_id',
            "value"=>function($model){
                $user = User::findOne($model->user_id);
                // if($user["username"] == "160044")
                // {
                //     $user["name"] = "超级管理员";
                // }
                return $user["name"];}
            ],
            [
            "label"=>"操作类型",
            'attribute' => 'type',
            "value"=>function($model){
                switch($model->type)
                {
                    case 1 : return '修改店铺定位坐标';
                    case 2 : return '登录/退出后台';
                    case 3 : return '用户管理模块';
                }
                }
            ],
            [
            'label'=>'操作内容',
            'format'=>'raw',
            'value' => function($model){
            return $model->log_title;
            }
            ],
            [
            'label'=>'操作时间',
            'format'=>'raw',
            'value' => function($model){
            return date('Y-m-d H:i:s',$model->add_time) ;
            }
            ],

            [
             'header' => "详情",
             'class' => 'yii\grid\ActionColumn',
             'template'=> '{view}',
             'headerOptions' => ['width' => '60'],
            ]
        ],
        'pager'=>[
           // 'options'=>['class'=>'hidden'],//关闭自带分页
            'firstPageLabel'=>"首页",
            'prevPageLabel'=>'上一页',
            'nextPageLabel'=>'下一页',
            'lastPageLabel'=>'尾页',
        ],
    ]); ?>
</div>
