<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\User;
use backend\models\UserBusiness;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserBusinessNotesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '跟进记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-business-notes-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'id',
//            'business_id',
            ['label' => "客户名称",
                "value" => function ($model) {
                    if (!empty($model->business_id)) {
                        $userdata = UserBusiness::find()->select(["customer_name"])->where(["id" => $model->business_id])->one();
                        return $userdata["customer_name"];
                    }
                }],
            'staff_num',
//            'time',
            ['label' => "姓名",
                "value" => function ($model) {
                    if (!empty($model->staff_num)) {
                        $userdata = User::find()->select(["name"])->where(["username" => $model->staff_num])->one();
                        return $userdata["name"];
                    }
                }],
            ['label' => "提交内容",
                "value" => function ($model) {
                    return strlen($model->followup_text)>40 ?mb_substr($model->followup_text,0,40,'utf-8')."...":$model->followup_text ;
                }],
//            'followup_text',
            ['label' => "记录时间",
                "value" => function ($model) {
                    return date("Y-m-d H:i:s", $model->time);
                }],
//            [
//                'header' => "是否作废",
//                'class' => 'yii\grid\ActionColumn',
//                'template'=> '{audit}',
//                'headerOptions' => ['width' => '100'],
//                'buttons'=>[
//                    'audit'=>function($url, $model, $key){
//                        if($model->is_show ==1)
//                        {
//                            return Html::a('作废', ['change', 'id' => $key,'flage' =>'1'], ['class'=>'btn btn-sm btn-danger']);
//                        }
//                        else
//                        {
//                            return Html::a('取消作废', ['change', 'id' => $key,'flage' =>'2'], ['class'=>'btn  btn-info']);
//                        }
//                    }
//                ]
//            ],
            [
                'header' => "详情",
                'class' => 'yii\grid\ActionColumn',
                'template'=> '{view}',
                'headerOptions' => ['width' => '60'],
            ],

//            ['class' => 'yii\grid\ActionColumn'],
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
