<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\UserWork;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSignSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = "用户考勤";
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-sign-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php $areaid = isset($areaid) ? $areaid : 0; ?>
    <?php echo $this->render('_search', ['model' => $searchModel,'areaid' => $areaid  ]); ?>
<?php
// echo "<pre>";
// print_r($dataProvider);
// echo "</pre>"; 
// exit();
?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
            'label'=>'用户名',
            'format'=>'raw',
            'value' => function($model){
                return !empty($model->user)?isset($model->userOne)?$model->userOne->username:'用户不存在':'用户ID丢失' ;
                }
            ],
            [
            'label'=>'姓名',
            'format'=>'raw',
            'value' => function($model){
                return !empty($model->user)?isset($model->userOne)?$model->userOne->name:'用户不存在':'用户id丢失' ;
                }
            ],
            [
            'label'=>'考勤情况',
            'format'=>'raw',
            'value' => function($model){
                    switch ($model->source_type)
                    {
                        case '1':
                            return '云管理';
                            break;
                        default:
                            return '考勤机';                      
                    }
                        
                }
            ],
            [
            'label'=>'考勤地址',
            'format'=>'raw',
            'value' =>  function($model){
                return !empty($model->path)?$model->path:'' ;
                }
            ],
            [
            'label'=>'考勤设备',
            'format'=>'raw',
            'value' =>  function($model){
                     $res = \backend\models\User::find()
                        ->select('phone_brand')
                        ->where(['id'=>$model->user])
                        ->asArray()
                        ->one();
                    return $res['phone_brand'];
                }
            ],
            [
            'label'=>'备注',
            'format'=>'raw',
            'value' =>  function($model)
            {
                return !empty($model->remarks)?mb_substr($model->remarks,0,10,'utf-8')."...":'' ;
            }
            ],
            [
            'label'=>'时间',
            'format'=>'raw',
            'value' => function($model){
            return date('Y-m-d H:i:s',$model->time) ;
            }
            ],
            [
            'label'=>'时间状态',
            'format'=>'raw',
            'value' => function($model)
            {
                    switch ($model->is_late)
                    {
                        case 0:
                            return '正常';
                            break;
                        case 1:
                            return '迟到'.$model->is_late_time.'分';
                            break;
                        case 2:
                            return '早退'.$model->is_late_time.'分';                      
                    }      
            }
            ],
            [
                'label'=>'照片',
                'format'=>'raw',
                'value'=>function($model){
                     //return Html::img('http://ngh.crm.openapi.xunmall.com/'.$model->image,['width' => 80]);
                   return Html::img("http://dev.crm.openapi.xunmall.com/".$model->image,['width' => 80]);
                }
            ],
            [
             'header' => "详情",
             'class' => 'yii\grid\ActionColumn',
             'template'=> '{view}',
             'headerOptions' => ['width' => '60'],
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
