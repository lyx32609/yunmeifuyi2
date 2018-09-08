<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\Regions;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'User').Yii::t('app','Group');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-group-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create').' '.$this->title, ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

         //   'id',
            
            'name',
            [
            'label'=>'部门',
            'value'=>'department.name',
            ],
            [
            'label'=>'地区',
            'value'=>function($model){
                    $city = Regions::find()
                            ->select(["local_name","p_region_id"])
                            ->where(["region_id" => $model->domain_id])
                            ->asArray()
                            ->one();
                    $area = Regions::find()
                            ->select(["local_name"])
                            ->where(["region_id" => $city["p_region_id"]])
                            ->asArray()
                            ->one();
                    return $area["local_name"].$city["local_name"];
            }
            ],
            'desc',
            'priority',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
