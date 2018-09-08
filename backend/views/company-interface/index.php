<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\CompanyCategroy;
use backend\models\CompanyInterface;
use backend\models\CompanyInterfaceModule;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '企业接口管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-interface-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('新增企业接口', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
            'label'=>'所属企业',
            'format'=>'raw',
            'value' => function($model){
                $company_data = CompanyCategroy::find()->select(["name"])->where(["id"=>$model->company_id])->one();
                return $company_data['name'];
            }
            ],
             [
                'label'=>'所属模块',
                'format'=>'raw',
                'value' => function($model){
                $company_data = CompanyInterfaceModule::find()->select(["module_name"])->where(["id"=>$model->module_id])->one();
                return $company_data['module_name'];
            }
             ],
             'url:url',
            [
                'label'=>'创建时间',
                'format'=>['date', 'php:Y-m-d'],
                'value' => 'createtime'
             ],
            // 'createtime',
            // 'protocol',

            ['class' => 'yii\grid\ActionColumn'],
        ],
            'pager'=>[
            'firstPageLabel'=>"首页",
            'prevPageLabel'=>'上一页',
            'nextPageLabel'=>'下一页',
            'lastPageLabel'=>'尾页',
        ],
    ]); ?>
</div>
