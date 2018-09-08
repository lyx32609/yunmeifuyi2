<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderDelivery */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '发车详情'), 'url' => ['index']];
$this->params['breadcrumbs'][] = "$this->title";
?>
<div class="order-delivery-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php 
    if($model->status == 0){$status = "作废";}
    if($model->status == 1){$status = "扫码装车";}
    if($model->status == 2){$status = "已发车";}
    if($model->status == 3){$status = "已签收";}

    if($model->batch_status == 0){$batch_status = "作废";}
    if($model->batch_status == 1){$batch_status = "正常";}
    if($model->batch_status == 2){$batch_status = "完成";}

    if($model->pay_sign_status == 1){$pay_sign_status = "待收款";}
    if($model->pay_sign_status == 2){$pay_sign_status = "已收款";}
    ?>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ["label"=>"订单id",
            'format'=>'raw',
            "value"=>$model->order_id
            ],
            ["label"=>"用户id",
            "value"=> $model->user_id
            ],
            ["label"=>"采购商id",
            "value"=>$model->member_id
            ],
            ["label"=>"车辆编号",
            "value"=> $model->car_id
            ],
            ["label"=>"状态",
            "value"=>$status
            ],
            ["label"=>"扫码发货时间",
            "value"=>$model->scan_time
            ],
            ["label"=>"发车时间",
            "value"=>$model->depart_time
            ],
            ["label"=>"签收时间",
            "value"=>$model->sign_for_time
            ],
            ["label"=>"车次的编号",
            "value"=>$model->batch_no
            ],
            ["label"=>"车次状态",
            "value"=>$batch_status
            ],
            // ["label"=>"收款状态",
            // "value"=>$pay_sign_status
            // ],
        ],
    ]) ?>

</div>
