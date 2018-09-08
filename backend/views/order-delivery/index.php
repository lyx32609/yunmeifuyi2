<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrderDeliverySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '发车详情');
$this->params['breadcrumbs'][] = '发车详情';
?>
<div class="order-delivery-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', '新增发车详情'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ["label"=>"订单id",
            "value"=>function($model){
                return $model->order_id;
            }
            ],
            ["label"=>"用户id",
            "value"=>function($model){
                return $model->user_id;
            }
            ],
            ["label"=>"采购商id",
            "value"=>function($model){
                return $model->member_id;
            }
            ],
            ["label"=>"车辆编号",
            "value"=>function($model){
                return $model->car_id;
            }
            ],
            ["label"=>"车辆编号",
            "value"=>function($model){
                switch($model->status){
                    case 0:return "作废";
                    case 1:return "扫码装车";
                    case 2:return "已发车";
                    case 3:return "已签收";
                }
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
