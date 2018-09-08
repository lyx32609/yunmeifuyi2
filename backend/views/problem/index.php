<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\UserDepartment;
use backend\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\problemSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '业务问题';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="problem-index">

    <h1>业务问题</h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
            'label'=>'问题标题',
            'format'=>'raw',
            'value' => function($model){
                return $model->problem_title ;
                }
            ],
            [
            'label'=>'创建人',
            'format'=>'raw',
            'value' => function($model){
                return $model->user_name ;
                }
            ],
            [
            'label'=>'部门',
            'format'=>'raw',
            'value' => function($model){
                $department_id = User::find()
                            ->select(["department_id"])
                            ->where(["id" => $model->user_id])
                            ->one();
                $department = UserDepartment::find()
                            ->select(["name"])
                            ->where(["id" => $department_id])
                            ->one();
                return  $department["name"];
                }
            ],
            [
            'label'=>'协同部门',
            'format'=>'raw',
            'headerOptions' => ['width' => '200'],
            'value' => function($model)
            {
                if($model->collaboration_department != "null")
                {
                    $department =  explode(",",$model->collaboration_department);
                    for($i=0;$i<count($department);$i++)
                    {
                        $p[$i] = UserDepartment::find()->where(["id"=>$department[$i]])->one();
                        $deprt[] = $p[$i]['name'];
                    }
                    if(count($deprt)>5)
                    {
                        $deprt = array_slice($deprt,0,5);
                    }
                    $depart_name = join("  ， ",$deprt);
                    return $depart_name.'  ...';
                }
                else
                {
                    return $model->collaboration_department;
                }

            }
            ],
            [
            'label'=>'优先级',
            'format'=>'raw',
            'value' => function($model){
                switch ($model->priority)
                    {
                        case 1:return "一级";
                        case 2:return "二级";
                        case 3:return "三级";
                    }
                }
            ],
            [
            'label'=>'创建时间',
//            'format'=>'raw',
             'attribute' => 'create_time',
            'value' => function($model){
                return date('Y-m-d H:m:s',$model->create_time) ;
                }
            ],
            [
             'header' => "操作",
             'class' => 'yii\grid\ActionColumn',
             'template'=> '{view}',
             'headerOptions' => ['width' => '100'],
            ],
            [
             'header' => "",
             'class' => 'yii\grid\ActionColumn',
             'template'=> '{audit}',
             'headerOptions' => ['width' => '100'],
             'buttons'=>[
                'audit'=>function($url, $model, $key){
                    if(in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))
                    {
                        return Html::a('删除', ['delete', 'id' => $key], ['class'=>'btn btn-sm btn-danger']);
                    }
                    else
                    {
                        return Html::a('查看', ['view', 'id' => $key], ['class'=>'btn btn-sm btn']);
                    }
                }
             ]
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
