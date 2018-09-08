<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\LinkPager;
use backend\models\CompanyCategroy;
use backend\models\User;
use backend\models\UserDepartment;
use backend\models\Percentum;
use backend\models\Record;
use backend\models\Orders;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\OrdersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '提成支付';
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .label-blue{
        background: #fff !important; 
    }
    .label-red{background-color:#ccc !important; }
    .hide{display:none;}
</style>
<div class="orders-index">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>
    <table  class="table table-striped table-bordered"><tr><td width="75%" ></td><td bgcolor="#ff8566" align='center'>未支付提成总金额<h4>￥<?php echo $non['num'].'元' ?></h4></td><td bgcolor="#00bfff" align='center'>已支付提成总金额<h4>￥<?php echo $paed['num'].'元' ?></h4></td></tr></table>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions' => function($model, $key, $index, $grid) {
        return ['class' => $model->pay_status == 2 ? 'label-blue' :'label-red'];
        },//状态不同 行颜色不同
        'showFooter' => true,  //设置显示最下面的footer

        'id' => 'grid',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label' => '用户名',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->staff_num;
                },
                'options' => [ 'width' => 60],
            ],
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
            [
                'label' => '订单金额',
                'format' => 'raw',
                'value' => function ($model) {
                    return round($model->payed,2) . "元";
                },
                'options' => [ 'width' => 60],
            ],
            [
                'label' => '完成时间',
                'format' => 'raw',
                'value' => function ($model) {
                    return date('Y-m-d H:i:s', $model->finishtime);
                }
            ],
            [
                'label' => '提成比例',
                'format' => 'raw',
                'value' => function ($model) {
                    return  $model->percent.'%';
                }
            ],
            [
                'label' => '提成金额',
                'format' => 'raw',
                'value' => function ($model) {
                    return  $model->money;
                },
            ],
            [
                'label' => '支付状态',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->pay_status == 1) {
                        return '未支付';
                    } else {
                        return '已支付';
                    }
                },
                'options' => [
                    'width' => 40
                ]
            ],
            [
                'label' => '支付人',
                'format' => 'raw',
                'value' => function ($model) {
                    $user_data = User::find()
                        ->select(['name'])
                        ->where(['id'=>$model->pay_uid])
                        ->asArray()
                        ->one();
                    return $user_data['name'];
                },
                'options' => [
                    'width' => 40
                ]
            ],
            [
                'label' => '支付人账号',
                'format' => 'raw',
                'options' => ['width' => 90],
                'value' => function ($model) {
                    $user_data = User::find()
                        ->select(['username'])
                        ->where(['id'=>$model->pay_uid])
                        ->asArray()
                        ->one();
                    return $user_data['username'];
                }
            ],
            [
                'label' => '支付时间',
                'format' => 'raw',
                'value' => function ($model) {
                    if($model->pay_time){
                        return date('Y-m-d H:i:s', $model->pay_time);
                    }
                    else{
                        return "";
                    }
                    
                }
            ],
            [
                'header' => "全选",
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'id',  //设置每行数据的复选框属性
                'headerOptions' => ['width' => '20'],
                'options' => [
                    'id' => 'grid',
                    'class' => 'hide'
                ],
                 'footer' => Html::input('checkbox', 'id_all','select-on-check-all'), 
            ],
            [
                'header' => "支付",
                'class' => 'yii\grid\ActionColumn',
                'template' => '{update}',
                'buttons' =>
                [
                    'update' => function ($url, $model, $key)
                    {
                        if ($model->pay_status == 1)
                            return Html::a('支付', ['pay', 'id' => $key], ['class' => 'btn btn-sm btn-info gridview2','data' => ['confirm' => '确定支付'.$model->money.'元吗？']]);
                    }
                ],
                'footer' => Html::a('批量支付', "javascript:void(0);", ['class' => 'btn btn-sm btn-info gridview1']),
                'options' => [
                    'width' => 100
                ],

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
<script>
    $(function(){

        $(".gridview1").on("click", function () 
        {
            var keys = $("#grid").yiiGridView("getSelectedRows");
            if(keys != "")
            {
                    /*选中求和start*/
                    $.ajax({
                    type: "GET",
                    url: "/pay-order/get-sum?ids=" + keys,
                    async: false,
                    success: function (sum) 
                        {
                            if(window.confirm("确认支付"+sum+'元吗？')== true)
                            {
                                $.ajax({
                                    type: "GET",
                                    url: "/pay-order/pay?ids=" + keys,
                                    async: false,
                                    success: function (data) {
                                        if(data==1){
                                            alert('支付成功!');
                                            location.reload();
                                        }
                                    }
                                });
                            }
                        }
                    });    
                    /*选中求和end*/
            }
            else
            {
                alert("请选择要支付的订单!");
            }

        });
    })

</script>
