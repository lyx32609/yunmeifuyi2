<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\ShopNote */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '回访记录'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-note-view">

    <h1><?= Html::encode($this->title) ?></h1>  
    <?php if($model->belong == 1){$belong = "采购商";}
          if($model->belong == 2){$belong = "代理商";}
          $time = date("Y-m-d",$model->time);
    ?>
    <p>
<!--         <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?> -->
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                "label"=>"商家id",
                "value"=>$model->shop_id
            ],
            [
                "label"=>"备注",
                "value"=>$model->note
            ],
            [
                "label"=>"时间",
                "value"=>$time
            ],
            [
                "label"=>"提交内容",
                "value"=>$model->conte
            ],
            [
                "label"=>"提交人账号",
                "value"=>$model->user
            ],
            [
                "label"=>"提交人经度",
                "value"=>$model->longitude
            ],
            [
                "label"=>"提交人纬度",
                "value"=>$model->latitude
            ],
            [
                'attribute' => 'imag',
                "label"=>"图片",
                'format' => "raw",
                "value"=>$model->getImag($model->imag)
            ],
            [
                "label"=>"类型",
                "value"=>$belong
            ],
        ],
    ]) ?>

</div>
