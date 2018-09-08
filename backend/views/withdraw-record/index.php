<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\User;
use backend\models\UserDepartment;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\WithdrawRecordSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '提现记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withdraw-record-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label'=>'账号',
                'value' =>  function($model)
                {
                    return $model->staff_num;
                }
            ],
            [
                'label'=>'姓名',
                'value' =>  function($model)
                {
                    $data = User::find()
                        ->select('name')
                        ->where(['username'=>$model->staff_num])
                        ->asArray()
                        ->one();
                    return $data['name'];
                }
            ],
            [
                'label'=>'部门',
                'value' =>  function($model)
                {
                    $data = UserDepartment::find()
                        ->from('off_user_department as d')
                        ->select('d.name')
                        ->leftJoin('off_user as u','u.department_id = d.id')
                        ->where(['username'=>$model->staff_num])
                        ->asArray()
                        ->one();
//                    var_dump($data);die;
                    return $data['name'];
                }
            ],
            [
                'label'=>'金额',
                'value' =>  function($model)
                {
                    return $model->money.'元';
                }
            ],
            [
                'label' => '时间',
                'format' => 'raw',
//                'options' => ['width' => 40],
                'value' => function ($model) {
                    return date('Y-m-d H:i:s', $model->time);
                }
            ],
            // 'flag',
            // 'order_id',
            // 'service_fee',

//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
