<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ShopSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '客户管理';
$this->params['breadcrumbs'][] = $this->title;



?>
<div class="shop-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'shop_name',
            'name',
            'phone',
//            'shop_type',
            ['label'=>"客户类型",
                "value"=>function($model)
                {
                    if ($model->shop_type == 1){
                        return '生产商';
                    }elseif ($model->shop_type == 2){
                        return '供货商';
                    }elseif ($model->shop_type == 3){
                        return '采购商';
                    }elseif ($model->shop_type == 4){
                        return '配送商';
                    }elseif ($model->shop_type == 5){
                        return '店铺商';
                    }elseif ($model->shop_type == 6){
                        return '运营商';
                    }elseif ($model->shop_type == 7){
                        return '销售商';
                    }elseif ($model->shop_type == 8){
                        return '服务商';
                    }
                }],
            // 'shop_source',
            // 'shop_status',
            // 'shop_priority',
            // 'shop_longitude',
            // 'shop_latitude',
            // 'shop_image',
            'user_name',
            // 'user_id',
            // 'company_category_id',
            // 'shop_review',
            'shop_addr',
            // 'shop_domain',
            ['label'=>"新增时间",
                "value"=>function($model)
                {
                    return date("Y-m-d H:i:s",$model->createtime);
                }],
            // 'shop_title',
            // 'shop_describe',

            [
                'header' => "操作",
                'class' => 'yii\grid\ActionColumn',
                'template'=> '{view} {delete}',
                'headerOptions' => ['width' => '60'],
            ],
        ],
    ]);

    ?>
</div>
