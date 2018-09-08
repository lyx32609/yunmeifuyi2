<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrderBatchSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '车次详情');
$this->params['breadcrumbs'][] = "车次详情";
?>
<div class="order-batch-index">

    <h1><?= Html::encode("车次详情") ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', '新建车次详情'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['label'=>'用户id',
            'value'=>function($model){
                return $model->user_id;
            }
            ],
            ['label'=>'车辆编号',
            'value'=>function($model){
                return $model->car_id;
            }
            ],
            ['label'=>'车辆名称',
            'value'=>function($model){
                return $model->car_name;
            }
            ],
            ['label'=>'司机姓名',
            'value'=>function($model){
                return $model->car_driver_name;
            }
            ],

            ['class' => 'yii\grid\ActionColumn'],
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
