<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderBatch */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Order Batches'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-batch-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php 
    if($model->status == 0){$status = "作废批次";}
    if($model->status == 1){$status = "正常批次";}
    if($model->status == 2){$status = "结束批次";}
    if($model->status == 3){$status = "发车状态";}

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
            ['label'=>'用户id',
            'value'=> $model->user_id,
            ],
            ['label'=>'车辆编号',
            'value'=>$model->car_id,
            ],
            ['label'=>'车辆名称',
            'value'=>$model->car_name,
            ],
            ['label'=>'司机姓名',
            'value'=>$model->car_driver_name,
            ],
            ['label'=>'司机电话',
            'value'=>$model->car_driver_phone,
            ],
            ['label'=>'自己生成的批次号',
            'value'=>$model->batch_no,
            ],
            ['label'=>'wms 传过来的批次号',
            'value'=>$model->batch_wms,
            ],
            ['label'=>'状态',
            'value'=>$model->status,
            ],
            ['label'=>'开始时间',
            'value'=>$model->start_time,
            ],
            ['label'=>'结束时间',
            'value'=>$model->end_time,
            ]
        ]
    ]) ?>

</div>
