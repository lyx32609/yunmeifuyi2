<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSignSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app', 'User Location');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="user-sign-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php $areaid = isset($areaid) ? $areaid : 0; ?>
    <?php echo $this->render('_search', ['model' => $searchModel,'areaid' => $areaid  ]); ?>
<!--     <p> -->
        <?php // Html::a(Yii::t('app', 'Create User Sign'), ['create'], ['class' => 'btn btn-success']) ?>
<!--     </p> -->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
     //   'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
            'label'=>'店铺id',
            'format'=>'raw',
            'value' => function($model){
                return  $model->shop_id;
                }
            ],
            [
            'label'=>'店铺名',
            'format'=>'raw',
            'value' => function($model){
                return  $model->name;
                }
            ],
            [
            'label'=>'定位来源',
           //'format'=>'raw',
            'value' => function($model){
                    switch ($model->type)
                    {
                        case 0:
                            return '业务回访';
                            break;
                        case 1:
                            return '新增业务';
                            break;
                        default:
                            return '未记录';                      
                    }
                }
            ],
            [
            'label'=>'地区',
            'format'=>'raw',
            'value' => function($model){
                return !empty($model->domain)?isset($model->userDomain)?$model->userDomain->region:'未记录':'记录丢失' ;
                }
            ],
            [
            'label'=>'定位类型',
           //'format'=>'raw',
            'value' => function($model){
                    switch ($model->belong)
                    {
                        case 1:
                            return '采购商';
                            break;
                        case 2:
                            return '代理商';
                            break;
                        default:
                            return '默认业务跟进';                      
                    }
                }
            ],
    		// 'reasonable',
            [
            'label'=>'员工名',
            'format'=>'raw',
            'value' => function($model){
                return !empty($model->user)?isset($model->userOne)?$model->userOne->name:'用户不存在':'用户ID丢失' ;        
                }
            ],
            [
                'label'=>'是否合理',
                'value'=>'reasonable'
            ],
            
/*             [
            'label'=>'是否合理',
            'format'=>'raw',
            'value' => function($model){
                    switch ($model->reasonable)
                    {
                        case 1:
                            return '合理';
                            break;
                        case 2:
                            return '不合理';
                            break;
                        default:
                            return '未记录';                      
                    }
                        
                }
            ], */
            [
            'label'=>'时间',
            'format'=>'raw',
            'value' => function($model){
            return date('Y-m-d H:i:s',$model->time) ;
            }
            ],
            

/*              [
                 
                 'class' => 'yii\grid\ActionColumn',
                 'template'=>'{view}'
            ],  */
        ],
       // 'layout'=> '{items}<div class="text-right tooltip-demo">{pager}</div>', //分页样式
        'pager'=>[
           // 'options'=>['class'=>'hidden'],//关闭自带分页
            'firstPageLabel'=>"首页",
            'prevPageLabel'=>'上一页',
            'nextPageLabel'=>'下一页',
            'lastPageLabel'=>'尾页',
        ],
    ]); ?>
    

    
    
</div>
