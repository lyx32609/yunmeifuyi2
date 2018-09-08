<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserIndexSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'User Indices');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php // Html::a('Create User Index', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
      //  'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

        //    'id',
            [
            'label'=>'用户名',
            'format'=>'raw',
            'value' => function($model){
                return !empty($model->userid)?isset($model->userOne)?$model->userOne->name:'用户不存在':'用户ID丢失' ;
                }
            ],
            'visitingnum',
            // 'registernum',
            ['label'=>'累计注册量',
                'value'=>function($model){
                    return $model->registernum;
                }
            ],
            ['label'=>'累计自己注册',
                'value'=>function($model){
                    return $model->registernum;
                }
            ],
            'ordernum',
             'orderamount',
             'orderuser',
             'deposit',
             'maimaijinorder',
             'maimaijinamount',
             'maimaijinuser',
             //'inputtime:datetime',

            [
            'header' => "查看",
            'class' => 'yii\grid\ActionColumn',
            'template'=> '{view} ',
            'headerOptions' => ['width' => '60'],
            'buttons' => [
                'view' => function ($url, $model, $key) {
                return Html::a(Html::tag('span', '', ['class' => "glyphicon fa fa-eye"]), ['rank/view', 'id'=>$model->userid], ['class' => "btn btn-xs btn-success", 'title' => '查看']);
                }]
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
