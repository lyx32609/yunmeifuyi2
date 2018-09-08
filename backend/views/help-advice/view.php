<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\User;
use backend\models\Help;

/* @var $this yii\web\View */
/* @var $model backend\models\HelpAdvice */
$user_data = User::find()
    ->select(["name"])
    ->where(["id"=>$model->user_id])
    ->one();
$data = Help::find()
    ->select(["content"])
    ->where(["id" => $model->type])
    ->one();

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '反馈详情', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="help-advice-view">



    <h1><?= Html::encode($data['content']) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
            [
                'label'=>'用户名',
                'format'=>'raw',
                'value' => $user_data['name']
            ],
            [
                'label'=>'类型',
                'value' => $data['content']
            ],
//            'type',
            'advice',
            [
                'label'=>'时间',
                'value' => date('Y-m-d H:i:s',$model->time)
            ],
//            'time:datetime',
        ],
    ]) ?>

</div>
