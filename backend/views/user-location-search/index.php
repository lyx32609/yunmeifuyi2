<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserLocationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Locations';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-location-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  //echo $this->render('_search', ['model' => $searchModel]); ?>

<!--     <p> -->
        <?php // Html::a(Yii::t('app', 'Create User Sign'), ['create'], ['class' => 'btn btn-success']) ?>
<!--     </p> -->
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'shop_id',
            'bing_id',
            'name',
            'longitude',
            // 'latitude',
            // 'user',
            // 'time',
            // 'type',
            // 'domain',
            // 'belong',
            // 'reasonable',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>



















