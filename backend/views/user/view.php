<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\Regions;
use backend\models\UserDepartment;
use backend\models\CompanyCategroy;

/* @var $this yii\web\View */
/* @var $model backend\models\User */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php $domain = Regions::find()->where(["region_id"=>$model->domain_id])->asArray()->one();
          $domain_name = $domain["local_name"];

          $company = CompanyCategroy::find()->where(["id" => $model->company_categroy_id])->asArray()->one();
          $company_name = $company["name"];

          if($model->is_staff == 1)
          {
            $staff = "在职";
          }
          else
          {
            $staff = "离职";
          }

          if($model->include_department_id){
              $departments = explode(',',$model->include_department_id);
              foreach ($departments as $k=>$v){
                  $arr[] = UserDepartment::find()
                      ->select('name')
                      ->where(['id'=>$v])
                      ->asArray()
                      ->one();
              }
              $arr = array_column($arr,'name');
              $department_name = implode('，',$arr);
          }else{
              $department_name = "暂不属于其他部门";
          }

    ?>
    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label'=>'ID',
                'value'=>$model->staff_code,
            ],
            [
            'label'=>'职务级别',
            'value'=>Yii::$app->params['rank'][$model->rank],
            ],
            [
                'label'=>'用户名（系统编号）',
                'value'=>$model->username,
            ],
            [
                'label'=>'姓名',
                'value'=>$model->name,
            ],
            [
                'label'=>'联系电话',
                'value'=>$model->phone,
            ],
            [
                'label'=>'分组',
                'value'=>isset($model->group->name)?$model->group->name:$model->group_id,
            ],
            [
                'label'=>'部门',
                'value'=>isset($model->department->name)?$model->department->name:$model->department_id,
            ],
            [
                'label'=>'所在公司',
                'value'=>$company_name
            ],

            [
                'label'=>'区域',
                'value'=>$domain_name
            ],

            [
                'label'=>'是否统计个人业务数据',
                'value'=>$model->is_select==0?'不统计':'统计',
            ],

            [
            'label'=>'是否在职员工',
            'value'=>$staff
            ],
            [
                'label'=>'所属部门（多个）',
                'value'=>$department_name,
            ],
        ],
    ]) ?>

</div>
