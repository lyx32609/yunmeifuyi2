<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ChildCompanyCategroySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app','子公司列表') ;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-categroy-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('新增子公司', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

         //   'id',
            [
            'label'=>'公司名称',
            //'format'=>'raw',
            'value' => function($model){
               return  $model->name;
            }
            ],
          //  'status',
        //    'createtime:datetime',
            [
            'label'=>'时间',
            'format'=>'raw',
            'value' => function($model){
            return date('Y-m-d H:i:s',$model->createtime) ;
            }
            ],
            [
            'label'=>'公司电话',
            //'format'=>'raw',
            'value' => function($model){
               return  $model->phone;
            }
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

            // ['class' => 'yii\grid\ActionColumn'],
            [
             'header' => "查看",
             'class' => 'yii\grid\ActionColumn',
             'template'=> '{view}',
             'headerOptions' => ['width' => '60'],
            ],
            [
             'header' => "修改",
             'class' => 'yii\grid\ActionColumn',
             'template'=> '{update}',
             'headerOptions' => ['width' => '60'],
            ],
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
