<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\UserDepartment;
use backend\models\CompanyCategroy;

/* @var $this yii\web\View */
/* @var $model backend\models\problem */

$this->title = $model->problem_id;
$this->params['breadcrumbs'][] = ['label' => '业务问题', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="problem-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php 
        /*协同部门*/
        if($model->collaboration_department != "null")
        {
                    $department =  explode(",",$model->collaboration_department);
                    for($i=0;$i<count($department);$i++)
                    {
                        $p[$i] = UserDepartment::find()->where(["id"=>$department[$i]])->one();
                        $deprt[] = $p[$i]['name'];
                    }
                    $depart_name = join("  ， ",$deprt);
                    $coll_department =  $depart_name;
        }
        else
        {
            $coll_department =  $model->collaboration_department;
        }
        if($model->priority == 1)
        {
            $priority = "一级";
        }
        if($model->priority == 2)
        {
            $priority = "二级";
        }
        if($model->priority == 3)
        {
            $priority = "三级";
        }
        if(!$model->update_time)
        {
            $update_time = '未完成';
        }
        else
        {
            $update_time = date('Y-m-d H:i:s',$model->update_time);
        }
        /*公司*/
        $company = CompanyCategroy::findOne($model->company_id)->name;
        //超级管理员可以删除或修改
        if(in_array(Yii::$app->user->identity->id, Yii::$app->params['through']))
        {
        ?>
        <p>
 <!--            <?= Html::a('Update', ['update', 'id' => $model->problem_id], ['class' => 'btn btn-primary']) ?> -->
            <?= Html::a('Delete', ['delete', 'id' => $model->problem_id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    <?php }?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label'=>'id',
                'value'=>$model->problem_id,
            ],
            [
                'label'=>'问题标题',
                'value'=>$model->problem_title,
            ],
            [
                'label'=>'问题内容',
                'value'=>$model->problem_content,
            ],
            [
                'label'=>'协同部门',
                'value'=>$coll_department,
            ],
            [
                'label'=>'优先级',
                'value'=>$priority
            ],
            [
                'label'=>'创建时间',
                'value'=>date('Y-m-d H:i:s',$model->create_time),
            ],
            [
                'label'=>'创建人',
                'value'=>$model->user_name,
            ],
            [
                'label'=>'是否完成',
                'value'=>$model->problem_lock==1?'完成' : '未完成',
            ],
            [
                'label'=>'完成时间',
                'value'=>$update_time,
            ],
            [
                'label'=>'省',
                'value'=>$model->area,
            ],
            [
                'label'=>'市',
                'value'=>$model->city,
            ],
            [
                'label'=>'部门',
                'value'=>$model->department,
            ],
            [
                'label'=>'公司',
                'value'=>$company,
            ],
        ],
    ]) ?>

</div>
