<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\BannerSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '首页图片';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="banner-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <p>
        <?php //echo  Html::a('上传图片', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'start_time',
                'value'=>
                    function($model){
                        return  date('Y-m-d',$model->start_time);   //主要通过此种方式实现
                    },
            ],
            [
                'attribute' => 'end_time',
                'value'=>
                    function($model){
                        return  date('Y-m-d',$model->end_time);   //主要通过此种方式实现
                    },
            ],
            [
                'label'=>'图片',
                'format'=>'raw',
                'value'=>function($model){
                    // return Html::img('http://ngh.crm.openapi.xunmall.com/'.$model->imag,['width' => 80]);
//                    return Html::img("http://crm.openapi.xunmall.com/".$model->images,['width' => 80]);
                    return Html::img(Yii::$app->urlManager->createAbsoluteUrl("$model->images"),['width' => 80]);
                }
            ],
            'is_valid'=>[
                'attribute' =>'版本',
                'value' =>function($model){
                    return $model->is_valid;
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=> '{update}',
                'headerOptions' => ['width' => '60'],
            ],
        ],
    ]); ?>
</div>
