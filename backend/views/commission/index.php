<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use backend\models\CompanyCategroy;
use backend\models\User;
use backend\models\UserDepartment;
use backend\models\Percentum;
use backend\models\Record;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrdersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '提成审核';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .label-blue{
        background: #fff !important;
    }
    .label-red{background-color:#ccc !important; }
</style>

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
            'staff_num',
            [
                'label' => '姓名',
                'format' => 'raw',
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
                'options' => ['width' => 40],
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
            'order_id',
            [
                'label' => '完成时间',
                'format' => 'raw',
                'options' => ['width' => 40],
                'value' => function ($model) {
                    return date('Y-m-d H:i:s', $model->finishtime);
                }
            ],
            [
                'label' => '订单金额',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->payed . "元";
                }
            ],
            [
                'label' => '提成比例',
                'format' => 'raw',
                'value' => function ($model) {
                    $order_time = $model->finishtime;
                    $record_model = Record::find()
                        ->select('percent,start_time,end_time')
                        ->asArray()
                        ->all();
                    $length = count($record_model);
//                    var_dump($length);die;
                    //先判断该订单是否大于off_record表中的最新时间，
                    //如果大于就去off_percentum表中查找提成比例
                    if ($order_time > $record_model[$length - 1]['end_time']) {
                        $percentum_data = Percentum::find()
                            ->select(['new_per'])
                            ->where(['is_open' => '1'])
                            ->asArray()
                            ->one();
                        return $percentum_data['new_per'] . "%";
                    } else {
                        $record_data = Record::find()
                            ->select(['id', 'start_time', 'end_time', 'percent'])
                            ->orderBy('end_time desc')
                            ->asArray()
                            ->all();
                        foreach ($record_data as $k => $v) {
                            if ($order_time >= $v['start_time'] && $order_time <= $v['end_time']) {
                                return $v['percent'] . "%";
                            }

                        }
                    }
                }
            ],
            [
                'label' => '提成金额',
                'format' => 'raw',
                'value' => function ($model) {
                    $order_time = $model->finishtime;
                    $record_model = Record::find()
                        ->select('percent,start_time,end_time')
                        ->asArray()
                        ->all();
                    $length = count($record_model);
                    //先判断该订单是否大于off_record表中的最新时间，
                    //如果大于就去off_percentum表中查找提成比例
                    if ($order_time > $record_model[$length - 1]['end_time']) {
                        $percentum_data = Percentum::find()
                            ->select(['new_per'])
                            ->where(['is_open' => '1'])
                            ->asArray()
                            ->one();
                        $money = $model->payed * $percentum_data['new_per'] / 100;
                        return $money . '元';
                    }
                    else {
                        $record_data = Record::find()
                            ->select(['id', 'start_time', 'end_time', 'percent'])
                            ->orderBy('end_time desc')
                            ->asArray()
                            ->all();
                        foreach ($record_data as $k => $v) {
                            if ($order_time >= $v['start_time'] && $order_time <= $v['end_time']) {
                                $money = $model->payed * $v['percent'] / 100;
                                return $money . '元';
                            }

                        }
                    }
                }
            ],
            [
                'label' => '审核时间',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->check_time?date('Y-m-d H:i:s', $model->check_time):'';
//                    return date('Y-m-d H:i:s', $model->check_time);
                }
            ],

            [
                'label' => '审核状态',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->check_status == 1) {
                        return '未审';
                    } else {
                        return '已审';
                    }
                }
            ],
            [
                'label' => '审核人',
                'format' => 'raw',
                'value' => function ($model) {
                    $user_data = User::find()
                        ->select(['name'])
                        ->where(['id'=>$model->check_uid])
                        ->asArray()
                        ->one();
                    return $user_data['name'];
                }
            ],
            [
                'label' => '审核人账号',
                'format' => 'raw',
                'options' => ['width' => 90],
                'value' => function ($model) {
                    $user_data = User::find()
                        ->select(['username'])
                        ->where(['id'=>$model->check_uid])
                        ->asArray()
                        ->one();
//                    var_dump($user_data['username']);die;
                    return $user_data['username'];
                }
            ],
            [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'id',  //设置每行数据的复选框属性
                'headerOptions' => ['width' => '20'],
                'options' => [
                    'id' => 'grid',
                ],
                'footer' => Html::a('批量审核', "javascript:void(0);", ['class' => 'btn btn-success gridview']),
                'footerOptions' => ['colspan' => 2],  //设置删除按钮垮列显示；
            ],
            [
                'header' => "审核",
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
                'buttons' => [
                    'update' => function ($url, $model, $key) {
                        if ($model->check_status == 1)
                            return Html::a('审核', ['check', 'id' => $key], ['class' => 'btn btn-sm btn-info']);
                    }
                ],
                'options' => [
                    'width' => 50
                ]
            ],
        ],
    ]);
    ?>

</div>
<script>
    $(function(){
        $(".gridview").on("click", function () {
            var keys = $("#grid").yiiGridView("getSelectedRows");
            var flage = confirm("确认审核通过");
            if(flage ==true){
                $.ajax({
                    type: "GET",
                    url: "/commission/check?ids=" + keys,
                    async: false,
                    success: function (data) {
//                    console.log(data);
                        if(data==1){
                            location.reload();
                        }
                    }
                });
            }
            else {
                location.reload();
            }
        });
    })

</script>