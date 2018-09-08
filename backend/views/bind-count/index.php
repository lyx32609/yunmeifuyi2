<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\UserDepartment;
use backend\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\BindCountSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '用户关联';
$this->params['breadcrumbs'][] = $this->title;
?>
<script type="text/javascript">
    function go()
    {
        if(confirm('确认关联？')){
            document.getElementById("bind").submit();
        }
    }
</script>
<div class="bind-count-index">
    <h2>
        <form action="bind" method="get" id="bind" style="float: left">
            <?= Html::encode($this->title) ?>
            &nbsp;&nbsp;&nbsp;&nbsp;
            <span style="font-size: 18px">
                云管理账号:
                    <input type="text" name="local_count" value="" style="width: 100px;">&emsp;&emsp;
                关联账号:
                    <input type="text" name="other_count" value="" style="width: 100px;">&emsp;&emsp;
                关联部门:
                    <select name="other_department">
                      <option value="12">买买金项目部</option>
                      <option value="3">讯猫集采项目部</option>
                      <option value="7">讯猫便利店项目部</option>
                      <option value="9">云仓储项目部</option>
                    </select>
            </span>
                    <input type="button" onclick="go()" class="btn btn-success" value="关联" style="height: 30px; width: 60px; margin: 0;">


        </form>
        <div style="clear: both"></div>
    </h2>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'local_count',
            [
                'label'=>'姓名',
                'value' =>  function($model)
                {
                    $local_count= $model->local_count;
                    $data = User::find()
                        ->select('name')
                        ->where(['username'=>$local_count])
                        ->asArray()
                        ->one();
                    return $data['name'];
                }
            ],
            [
                'label'=>'部门',
                'value' =>  function($model)
                {
                    $department_id= $model->local_department;
                    $data = UserDepartment::find()
                        ->select('name')
                        ->where(['id'=>$department_id])
                        ->asArray()
                        ->one();
                    return $data['name'];
                }
            ],
            [
                'label'=>'手机号',
                'value' =>  function($model)
                {
                    $local_count= $model->local_count;
                    $data = User::find()
                        ->select('phone')
                        ->where(['username'=>$local_count])
                        ->asArray()
                        ->one();
                    return $data['phone'];
                }
            ],
            [
                'label'=>'关联部门',
                'value' =>  function($model)
                {
                    $department_id= $model->other_department;
                    $data = UserDepartment::find()
                        ->select('name')
                        ->where(['id'=>$department_id])
                        ->asArray()
                        ->one();
                    return $data['name'];
                }
            ],
            'other_count',
            [
                'label'=>'关联时间',
                'value' =>  function($model)
                {
                    $time= $model->time;
                    return date("Y-m-d H:i:s",$time);
                }
            ],
            [
                'label'=>'操作人',
                'value' =>  function($model)
                {
                    $operation_id= $model->operation_id;
                    $data = User::find()
                        ->select('name')
                        ->where(['id'=>$operation_id])
                        ->asArray()
                        ->one();
                    return $data['name'];
                }
            ],
            [
                'label'=>'操作人账号',
                'value' =>  function($model)
                {
                    $operation_id= $model->operation_id;
                    $data = User::find()
                        ->select('username')
                        ->where(['id'=>$operation_id])
                        ->asArray()
                        ->one();
                    return $data['username'];
                }
            ],
            [
                'label'=>'操作内容',
                'value' =>  function($model)
                {
                    return $model->operation_content;

                }
            ],

//            'local_department',
            // 'operation_id',
            // 'operation_content',
            // 'time:datetime',

            [
                'header' => "删除",
                'class' => 'yii\grid\ActionColumn',
                'template'=> '{delete}',
                'headerOptions' => ['width' => '60'],
            ],
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
