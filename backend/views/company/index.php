<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\CompanyCategroySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Company Categroys');
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
/*.table-striped>tbody>tr:nth-of-type(odd) {
    background: transparent;
}*/
/*.table-striped > tbody > tr:nth-of-type(odd){
    background: #00c0ef;
}*/
    .label-blue{
        background: #99ffd6 !important; 
    }
    .label-red{background-color:#cc3300 !important; }
    .label-green{background-color:#fff !important; }
</style>
<div class="company-categroy-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php // Html::a('Create Company Categroy', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'rowOptions' => function($model, $key, $index, $grid) {
        return ['class' => $model->review == 2 ? 'label-blue' :( $model->review == 3 ?'label-red' : 'label-green')];
    },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            /* 
            [
            'label'=>'注册id号',
            'format'=>'raw',
            'value' => 'id'
            ], */
            [
            'label'=>'企业名称',
            'format'=>'raw',
            'value' => 'name'
            ],
                        [
            'label'=>'审核状态',
            'format'=>'raw',
            'value' => function($model){
                //0运营 1销售 2供货 3配送 4生产 5服务
                if(($model->review == 0) ||  ($model->review == 1)){return "待审核";}
                if($model->review == 2){return "审核通过";}
                if($model->review == 3){return "审核未通过";}
                } 
            ],
            [
            'label'=>'企业类型',
            'format'=>'raw',
            'value' => function($model){
                //0运营 1销售 2供货 3配送 4生产 5服务
                if($model->status == 0){return "运营";}
                if($model->status == 1){return "销售";}
                if($model->status == 2){return "供货";}
                if($model->status == 3){return "配送";}
                if($model->status == 4){return "生产";}
                if($model->status == 5){return "服务";}
                } 
            ],
            [
            'label'=>'创建日期',
            'format' => ['date', 'php:Y-m-d'],
            'value' => 'createtime'
            ],
            [
            'label'=>'联系方式',
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
            
             [
             'header' => "查看",
             'class' => 'yii\grid\ActionColumn',
             'template'=> '{view} {check}',
             'headerOptions' => ['width' => '60'],
            ],
            [
            'header' => "审核",
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update}',
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    if(($model->review == 0) || $model->review == 1)
                    {
                        return Html::a('审核', ['check', 'id' => $key], ['class'=>'btn btn-sm btn-danger']);
                    }
                    else
                    {
                        return Html::a('查看', ['view', 'id' => $key], ['class'=>'btn btn-sm btn-info']);
                    }
                
                    // return Html::a('审核', ['check', 'id' => $key], ['class' => $model->review ? 'btn btn-sm btn-danger' : 'btn btn-sm btn-info']);
                }
                ],
                'options' => [
                    'width' => 5
                ]
                ],
          //  ['class' => 'yii\grid\ActionColumn'],
        ],
        'pager'=>[
           // 'options'=>['class'=>'hidden'],//关闭自带分页
            'firstPageLabel'=>"首页",
            'prevPageLabel'=>'上一页',
            'nextPageLabel'=>'下一页',
            'lastPageLabel'=>'尾页',
        ],
        'emptyText' => '暂时没有企业信息！',//没有数据时显示的信息
    ]); ?>
</div>
