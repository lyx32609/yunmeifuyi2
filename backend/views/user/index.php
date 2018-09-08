<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $rules = array_keys(\Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id));//角色hr可以看部门
     ?>

    <p>
        <?= Html::a(Yii::t('app', Yii::t('app', 'Create').' '.Yii::t('app', 'User')), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
        <?php $areaid = isset($areaid) ? $areaid : 0; ?>
    <?php echo $this->render('_search', ['model' => $searchModel,'areaid' => $areaid  ]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
            "label"=>"用户名",
            'attribute' => 'username',
            "value"=>function($model){
                return $model->username;}
            ],
            [
            "label"=>"用户姓名",
            'attribute' => 'name',
            "value"=>function($model){
                return $model->name;}
            ],
            [
            "label"=>"联系电话",
            'attribute' => 'phone',
            "value"=>function($model){
                return $model->phone;}
            ],
            [
            "label"=>"职务级别(1/3/4/30)",
            'attribute' => 'rank',
            "value"=>function($model){
                switch($model->rank)
                {
                    case 1:
                    $rules = array_keys(\Yii::$app->authManager->getRolesByUser($model->id));
                    if(in_array('deliver',$rules))
                    {
                        return "配送人员";
                    }
                    else
                    {
                        return "一线员工";
                    }
                    case 3:return "子公司经理";
                    case 4:return "部门经理";
                    case 30:return "主公司经理";
                }
            }
            ],
            [
                "label"=>"注册时间",
                'attribute' => 'create_time',
                "value"=>function($model){
                    return Date('Y-m-d H:m:s',$model->create_time);}
            ],
            [
            "label"=>"权限",
            "value"=>function($model){
                    $rules = array_keys(Yii::$app->authManager->getRolesByUser($model->id));
                    $rule = join(",",$rules);
                    //return Yii::$app->authManager->getRolesByUser($model->id);
                if(in_array(Yii::$app->user->identity->id,Yii::$app->params['through']))
                {
                    return $rule;
                }
                else
                {
                    return $rule;
                }
            }
            ],
            
            ['class' => 'yii\grid\ActionColumn'],
/*             [
            'label'=>'管理菜单',
            'format'=>'raw',
                'value' => function($model){
                     $url = "/user/menu-set?id=".$model->id;
                     return Html::a('分配菜单', $url, ['title' => '分配菜单']); 
                }
            ], */
            [
            'label'=>'清除串号',
            'format'=>'raw',
            'value' => function($model){
                    if(in_array(Yii::$app->user->identity->id,Yii::$app->params['through'])){
                        $url = "/user/clear?id=".$model->id;
                        return Html::a('', $url, ['title' => '清除串号','class'=>'glyphicon glyphicon-remove','style'=>'margin-left:20px;']);
                    }else{
                        return '暂无权限';
                    }
                }
            ],
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
