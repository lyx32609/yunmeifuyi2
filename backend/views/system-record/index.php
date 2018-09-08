<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\SystemRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '系统记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-record-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'staff_num',
            [
                "label" => "异常内容",
                'attribute' => 'content',
                "value" => function ($model) {
                    return strlen($model->content) > 80 ? mb_substr($model->content, 0, 80, 'utf-8') . "..." : $model->content;
                }
            ],
            'type',
            'brand_model',
            [
                "label" => "日期时间",
                'attribute' => 'time',
                "value" => function ($model) {
                    return date('Y-m-d H:i:s', $model->time);
                }
            ],
            [
                'header' => "查看",
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} ',
                'headerOptions' => ['width' => '60'],
            ],

        ],
    ]); ?>
</div>
