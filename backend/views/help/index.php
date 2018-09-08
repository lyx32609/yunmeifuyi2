<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\Help;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '注意事项及使用须知列表';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="help-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('创建一级分类', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('创建二级分类', ['create1'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('创建具体须知', ['create2'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'id',
//            'parent_id',
//            'son_id',
//            'type',
            [
                'label'=>'类别',
                'value' =>  function($model)
                {
                    if($model->type == 1){
                        return '注意事项';

                    }elseif ($model->type == 2){
                        return '使用须知';
                    }

                }
            ],
            [
                'label'=>'parent_id',
                'value' =>  function($model)
                {
                    $res = Help::find()
                        ->where(['id'=>$model->parent_id])
                        ->select(['content'])
                        ->asArray()
                        ->one();
                    // return $res['content'];
                    return strlen($res['content'])>20 ?mb_substr($res['content'],0,20,'utf-8')."...":$res['content'] ;
                }
            ],
            [
                'label'=>'son_id',
                'value' =>  function($model)
                {
                    $data = Help::find()
                        ->where(['id'=>$model->son_id])
                        ->select(['content'])
                        ->asArray()
                        ->one();
                    // return $data['content'];
                    return strlen($data['content'])>20 ?mb_substr($data['content'],0,20,'utf-8')."...":$data['content'] ;
                }
            ],
            [
                'label'=>'内容',
                'value' =>  function($model)
                {
                    return strlen($model->content)>20 ?mb_substr($model->content,0,20,'utf-8')."...":"$model->content" ;
                }
            ],
//             'sumup',
//             'sumdown',

            ['class' => 'yii\grid\ActionColumn'],
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
