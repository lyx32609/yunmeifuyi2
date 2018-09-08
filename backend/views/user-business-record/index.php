<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserBusinessSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '新增记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-business-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
            'customer_name',
            'customer_tel',
//            'customer_type',
//            'customer_source',
            // 'customer_state',
            // 'customer_priority',
            // 'customer_longitude',
            // 'customer_latitude',
            // 'customer_photo_str',
            // 'customer_business_title',
            // 'customer_business_describe',
            ['label' => "客户类型",
                "value" => function ($model) {

                    switch ($model->customer_type){
                        case 1:
                            return '生产商';
                            break;
                        case 2:
                            return '供货商';
                            break;
                        case 3:
                            return '采购商';
                            break;
                        case 4:
                            return '配送商';
                            break;
                        case 5:
                            return '店铺';
                            break;
                    }
                }],
            ['label' => "客户来源",
                "value" => function ($model) {

                    switch ($model->customer_source){
                        case 1:
                            return '开发';
                            break;
                        case 2:
                            return '网站';
                            break;
                        case 3:
                            return '展会';
                            break;
                        case 4:
                            return '介绍';
                            break;
                        case 5:
                            return '媒体';
                            break;
                    }
                }],
             'staff_num',
            ['label' => "姓名",
                "value" => function ($model) {
                    if (!empty($model->staff_num)) {
                        $userdata = User::find()->select(["name"])->where(["username" => $model->staff_num])->one();
                        return $userdata["name"];
                    }
                }],
            // 'time',

            ['label' => "新增时间",
                "value" => function ($model) {

                    return date("Y-m-d H:i:s", $model->time);
                }
            ],
            [
                'label'=>'图片',
                'format'=>'raw',
                'value'=>function($model){
                    // return Html::img('http://ngh.crm.openapi.xunmall.com/'.$model->imag,['width' => 80]);
//                    return Html::img("http://crm.openapi.xunmall.com/".$model->customer_photo_str,['width' => 80]);
                    return Html::img("http://dev.crm.openapi.xunmall.com/".$model->customer_photo_str,['width' => 80]);
                }
            ],
            [
                'header' => "查看详情",
                'class' => 'yii\grid\ActionColumn',
                'template'=> '{view}',
                'headerOptions' => ['width' => '75'],
            ],
//            [
//                'header' => "是否作废",
//                'class' => 'yii\grid\ActionColumn',
//                'template'=> '{audit}',
//                'headerOptions' => ['width' => '100'],
//                'buttons'=>[
//                    'audit'=>function($url, $model, $key){
//                        if($model->is_show ==1)
//                        {
//                            return Html::a('作废', ['change', 'id' => $key,'flage' =>'1'], ['class'=>'btn btn-sm btn-danger']);
//                        }
//                        else
//                        {
//                            return Html::a('取消作废', ['change', 'id' => $key,'flage' =>'2'], ['class'=>'btn  btn-info']);
//                        }
//                    }
//                ]
//            ],
            // 'domain_id',
            // 'customer_user',

//            ['class' => 'yii\grid\ActionColumn'],
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
