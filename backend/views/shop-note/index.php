<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\Shop;
use backend\models\User;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\ShopNoteSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', '回访记录');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-note-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php  Html::a(Yii::t('app', 'Create Shop Note'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            // ['label'=>"商家",
            // "value"=>function($model)
            // {  
            //     return $model->shop_id;
            // }],

            //             ['label'=>"备注",
            // "value"=>function($model)
            // {
            //     return $model->note;
            // }],
            ['label'=>"账号",
            "value"=>function($model)
            {
                return $model->user;
            }],
                        ['label'=>"姓名",
            "value"=>function($model)
            {
                if(!empty($model->user))
                {
                    $userdata = User::find()->select(["name"])->where(["username"=>$model->user])->one();
                    return $userdata["name"];
                }
            }],
            ['label'=>"提交内容",
            'headerOptions' => ['width' => '300'],
            "value"=>function($model)
            {
                return mb_substr($model->conte,0,20).'...';
            }],
            ['label'=>"时间",
            "value"=>function($model)
            {
                return date("Y-m-d H:i:s",$model->time);
            }],
            [
                'label'=>'图片',
                'format'=>'raw',
                'value'=>function($model){
                   // return Html::img('http://ngh.crm.openapi.xunmall.com/'.$model->imag,['width' => 80]);
//                     return Html::img("http://crm.openapi.xunmall.com/".$model->imag,['width' => 80]);
                     return Html::img("http://dev.crm.openapi.xunmall.com/".$model->imag,['width' => 80]);
                }
            ],
            [
             'header' => "查看详情",
             'class' => 'yii\grid\ActionColumn',
             'template'=> '{view}',
             'headerOptions' => ['width' => '75'],
            ],
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
