<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\CompanyCategroy;
use backend\models\User;
use backend\models\UserDepartment;
use backend\models\Percentum;
use backend\models\Record;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrdersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '提成记录';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="orders-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,

        'showFooter' => true,  //设置显示最下面的footer
        'id' => 'grid',
        'rowOptions' => function($model, $key, $index, $grid) {
            return ['class' => $model->check_status == 2 ? 'label-blue' :'label-red'];
        },
//        'filterModel' => $searchModel,
        'columns' => [

            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => '账号',
                'format' => 'raw',
                'options' => ['width' => 150],
                'value' => function ($model) {
                    return $model->staff_num;
                }
            ],
            [
                'label' => '姓名',
                'format' => 'raw',
                'options' => ['width' => 150],
                'value' => function ($model) {
                    $user_model = User::find()
                        ->select('name')
                        ->where(['username' => $model->staff_num])
                        ->asArray()
                        ->one();
                    return $user_model['name'];
                }
            ],
            [
                'label' => '部门',
                'format' => 'raw',
                'options' => ['width' => 150],
                'value' => function ($model) {
                    $company_model = CompanyCategroy::find()
                        ->select('d.name')
                        ->from(CompanyCategroy::tableName() . ' as c')
                        ->leftjoin(User::tableName() . ' As u', 'u.company_categroy_id =c.id')
                        ->leftjoin(UserDepartment::tableName() . ' As d', 'd.id =u.department_id')
                        ->where(['u.username' => $model->staff_num])
                        ->asArray()
                        ->one();
                    return $company_model['name'];
                }
            ],
//            'order_id',
//
            [
                'label' => '订单金额',
                'format' => 'raw',
                'value' => function ($model) {
                    return round($model->payed,2) . "元";
                }
            ],
            [    'label' => '提成比例',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->percent."%";
                }
            ],
            [
                'label' => '提成金额',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->money."元";
                }
            ],
            [
                'label' => '订单完成时间',
                'format' => 'raw',
                'options' => ['width' => 250],
                'value' => function ($model) {
                    return date('Y-m-d H:i:s', $model->finishtime);
                }
            ],

        ],
    ]);
    ?>

</div>
