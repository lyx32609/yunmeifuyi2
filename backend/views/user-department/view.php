<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\UserDepartment */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '部门列表'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-department-view">

    <h1><?= Html::encode($this->title) ?></h1>

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
            'id',
            'name',
            'desc',
            [
            'label'=>'地区',
            'value'=>isset($model->region->local_name)?$model->region->local_name:$model->domain_id,
            ],
          //  'domain.region',
            'priority',
            [
                'label'=>'是否统计个人业务数据',
                'value'=>$model->is_select==0?'不统计':'统计',
            ],
        ],
    ]) ?>

</div>
