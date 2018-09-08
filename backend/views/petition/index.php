<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\UserDepartment;
use backend\models\User;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\PetitionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '签呈记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="petition-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>



    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['label'=>'id',
                'value'=>function($model){
                    return $model->id;
                }
            ],
            [
                "label"=>"账号",
                "value"=>function($model){
                    $people = User::find()
                        ->select(["username"])
                        ->where(["id" => $model->uid])
                        ->asArray()
                        ->one();
                    return $people["username"];
                }
            ],
            [
                "label"=>"姓名",
                "value"=>function($model){
                    $people = User::find()
                        ->select(["name"])
                        ->where(["id" => $model->uid])
                        ->asArray()
                        ->one();
                    return $people["name"];
                }
            ],
            [
                "label"=>"部门",
                'attribute' => 'department_id',
                "value"=>function($model){
                    $company = UserDepartment::find()
                        ->select(["name"])
                        ->where(["id" => $model->department_id])
                        ->asArray()
                        ->one();
                    return $company["name"];
                }
            ],
            ['label'=>'签呈分类',
                'value'=>function($model){
                    switch($model->type)
                    {
                        //0通用1领用2用车3付款4报销5采购6用证7用印8出差9加班10请假11外出12转正13离职14招聘
                        case 0:return '通用';
                        break;
                        case 1:return '领用';
                        break;
                        case 2:return '用车';
                        break;
                        case 3:return '付款';
                        break;
                        case 4:return '报销';
                        break;
                        case 5:return '采购';
                        break;
                        case 6:return '用证';
                        break;
                        case 7:return '用印';
                        break;
                        case 8:return '出差';
                        break;
                        case 9:return '加班';
                        break;
                        case 10:return '请假';
                        break;
                        case 11:return '外出';
                        break;
                        case 12:return '转正';
                        break;
                        case 13:return '离职';
                        break;
                        case 14:return '招聘';
                    }
                }
            ],
            ['label'=>'审批人数',
                'value'=>function($model){
                    $ids = explode(',',$model->ids);
                    return count($ids);
                }
            ],
            ['label'=>'同意人数',
                'value'=>function($model){
                   $res =  \backend\models\Examine::find()
                        ->where(['status' =>1])
                        ->andWhere(['petition_id'=>$model->id])
                        ->asArray()
                        ->all();
                    return count($res);
                }
            ],
            ['label'=>'签呈状态',
                'value'=>function($model){
                    // return $model->status;
                    if(in_array($model->status,[2,3])){
                        return '审核中';
                    }
                    if($model->status == 5){
                        return '已完成已支付';
                    }
                    if($model->status == 6){
                        return '已完成未支付';
                    }
                    if($model->status == 7){
                        return '已作废';
                    }
                    if(in_array($model->status,[0,1,4])){
                        return '已完成';
                    }
                }
            ],
            ['label'=>'提交时间',
                'value'=>function($model){
                    return date('Y-m-d H:i:s', $model->create_time );
                }
            ],
            [
            'header' => "查看",
            'class' => 'yii\grid\ActionColumn',
            'template' => '{view}',
            'buttons' => [
                'view' => function ($url, $model, $key) {
                        return Html::a('查看', ['view', 'id' => $key], ['class'=>'btn btn-sm btn-success']);
                }
                ],
                'options' => [
                    'width' => 5
                ]
            ],
            [
            'header' => "",
            'class' => 'yii\grid\ActionColumn',
            'template' => '{update}',
            'buttons' => [
                'update' => function ($url, $model, $key) {
                    if(($model->uid == Yii::$app->user->identity->id) && ($model->status !== 7))
                    {
                        return Html::a('作废', ['invalid', 'id' => $key], ['class'=>'btn btn-sm btn-danger']);
                    }
                    $ids = explode(",",$model->ids);
                    if((in_array(Yii::$app->user->identity->id,$ids)) && in_array($model->status,[2,3,6]))
                    {
                        return Html::a('审批', ['update', 'id' => $key], ['class'=>'btn btn-sm btn-info']);
                    }

                }
                ],
                'options' => [
                    'width' => 5
                ]
            ],
            [
            'header' => "",
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}',
            'buttons' => [
                'delete' => function ($url, $model, $key) {
                    if((Yii::$app->user->identity->rank == 30) || (in_array(Yii::$app->user->identity->id, Yii::$app->params['through'])))
                    {
                        return Html::a('删除', ['delete', 'id' => $key], ['class'=>'btn btn-sm btn-danger']);

                    }
                    
                }
                ],
                'options' => [
                    'width' => 5
                ]
            ],
        ],
        'pager'=>[
            'firstPageLabel'=>"首页",
            'prevPageLabel'=>'上一页',
            'nextPageLabel'=>'下一页',
            'lastPageLabel'=>'尾页',
        ],
    ]); ?>
</div>
