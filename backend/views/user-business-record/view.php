<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\Regions;


/* @var $this yii\web\View */
/* @var $model backend\models\UserBusiness */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '新增记录', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
//var_dump($model->customer_type);die;
    switch ($model->customer_type){
        case 1:
            $customer_type = '生产商';
            break;
        case 2:
            $customer_type = '供货商';
            break;
        case 3:
            $customer_type = '采购商';
            break;
        case 4:
            $customer_type = '配送商';
            break;
        case 5:
            $customer_type = '店铺';
            break;
    }
    switch ($model->customer_source){
        case 1:
            $customer_source = '开发';
            break;
        case 2:
            $customer_source = '网站';
            break;
        case 3:
            $customer_source = '展会';
            break;
        case 4:
            $customer_source = '介绍';
            break;
        case 5:
            $customer_source = '媒体';
            break;
    }
    switch ($model->customer_state){
        case 1:
            $customer_state = '潜在';
            break;
        case 2:
            $customer_state = '意向';
            break;
        case 3:
            $customer_state = '已合作';
            break;
        case 4:
            $customer_state = '无意向';
            break;
    }

    $time = date('Y-m-d H:i:s',$model->time);
    $domin = Regions::find()
        ->select('local_name')
        ->where(['region_id'=>$model->domain_id])
        ->asArray()->one();
?>
<div class="user-business-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'customer_name',
            'customer_tel',
//            'customer_type',
            [
                'label'=>'客户类型',
                'value'=>$customer_type,
            ],
            [
                'label'=>'客户来源',
                'value'=>$customer_source,
            ],
            [
                'label'=>'客户状态',
                'value'=>$customer_state,
            ],
//            'customer_source',
//            'customer_state',
            'customer_priority',
            'customer_longitude',
            'customer_latitude',
//            'customer_photo_str',
            [
                'attribute' => 'imag',
                "label"=>"图片",
                'format' => "raw",
                "value"=>Html::img("http://dev.crm.openapi.xunmall.com/".$model->customer_photo_str,"",array("width"=>'200px','height'=>'200px'))
            ],
            'customer_business_title',
            'customer_business_describe',
            'staff_num',
            [
                'label'=>'时间',
                'value'=>$time,
            ],[
                'label'=>'地区',
                'value'=>$domin['local_name'],
            ],
//            'time:datetime',
//            'domain_id',
            'customer_user',
        ],
    ]) ?>

</div>
