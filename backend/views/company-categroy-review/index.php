<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '企业列表';
$this->params['breadcrumbs'][] = "企业注册审核";
?>
<div class="company-categroy-review-index">

    <h1><?= Html::encode($this->title) ?></h1>

<!--     <p>
        <?= Html::a('Create Company Categroy Review', ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            
            [
            'label'=>'企业名称',
            'format'=>'raw',
            'value' => 'name'
            ],
            [
            'label'=>'企业类型',
            'format'=>'raw',
            'value' => function($model){
                    switch ($model->status)
                    {
                        case 0:
                            return '运营';
                            break;
                        case 1:
                            return '销售';
                            break;
                        case 2:
                            return '供货';
                            break;
                        case 3:
                            return '配送';
                            break;
                        case 4:
                            return '生产';
                            break;
                        case 5:
                            return '服务';
                            break;
                        default:
                            return '企业不存在';                      
                    }
                        
                }
            ],
            [
            'label'=>'创建日期',
            'format' => ['date', 'php:Y-m-d'],
            'value' => 'createtime'
            ],
            [
            'label'=>'联系电话',
            'format'=>'raw',
            'value' => 'phone'
            ],
            // 'area_id',
            // 'domain_id',
            // 'fly',
            // 'type',
            // 'review',
            // 'license_num',
            // 'register_money',
            // 'business',
            // 'business_ress',
            // 'staff_num',
            // 'acting',
            // 'proxy_level',
            // 'service_area',
            // 'distribution_merchant',
            // 'distribution_car',
            // 'distribution_staff',
            // 'goods_num',
            // 'failure',
            // 'goods_type',
            // 'service_type',
            // 'product_type',
            // 'salas_business',
            // 'license_image',
            // 'user_image_negative',
            // 'user_image_positive',
            //  [
            //  'header' => "查看",
            //  'class' => 'yii\grid\ActionColumn',
            //  'template'=> '{view} {check}',
            //  'headerOptions' => ['width' => '60'],
            // ],
            [
            'header' => "审核",
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update}',
            'buttons' => [
                'update' => function ($url, $model, $key) {
                return Html::a('审核', ['company-categroy-review/create', 'id' => $key], ['class'=>'btn btn-sm btn-danger']);
                }
                ],
                'options' => [
                    'width' => 5
                ]
                ],
          //  ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
