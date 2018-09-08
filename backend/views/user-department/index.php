<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\Regions;
use backend\models\CompanyCategroy;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserDepartmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'User').Yii::t('app', 'Departments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-department-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', Yii::t('app','Create').' '.Yii::t('app','Departments')), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'label'=>'部门',
                'attribute'=>'name',
            ],
            [
                'label'=>'区域',
                //'attribute'=>'domain_id',
                'value'=>function($model)
                {
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
            [
                'label'=>'所属公司',
                //'attribute'=>'name',
                'value'=>function($model)
                {
                    $company = CompanyCategroy::find()
                            ->select(["name"])
                            ->where(["id" => $model->company])
                            ->asArray()
                            ->one();
                    return $company["name"];
                }
            ],
            'priority',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
