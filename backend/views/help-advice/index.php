<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\User;
use backend\models\Help;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '反馈意见';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="help-advice-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
//            'user_id',
            [
                'label'=>'用户名',
                'value' =>  function($model)
                {
                    $data = User::find()
                        ->select(["name"])
                        ->where(["id" => $model->user_id])
                        ->one();

                    return  $data["name"];
                }
            ],
//            'type',
            [
                'label'=>'类型',
                'value' =>  function($model)
                {
                    $data = Help::find()
                        ->select(["content"])
                        ->where(["id" => $model->type])
                        ->one();

                    return  $data["content"];
                }
            ],
//            'advice',
            [
                'label'=>'内容',
                'value' =>  function($model)
                {
                    return strlen($model->advice)>30?mb_substr($model->advice,0,30,'utf-8')."...":"$model->advice" ;
                }
            ],
            [
                'label'=>"时间",
                "value"=>function($model)
                {
                    return date("Y-m-d H:i:s",$model->time);
                }],
            [
                'header' => "查看",
                'class' => 'yii\grid\ActionColumn',
                'template'=> '{view}',
                'headerOptions' => ['width' => '80'],
            ],
//            'time:datetime',


//            ['class' => 'yii\grid\ActionColumn'],
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
