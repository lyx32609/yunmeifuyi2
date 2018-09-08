<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\CompanyCategroy;
use backend\models\CompanyInterfaceModule;

/* @var $this yii\web\View */
/* @var $model backend\models\CompanyInterface */

// $this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '企业接口管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-interface-view">

    <h1><?= Html::encode($this->title) ?></h1>
<?php 
        $company_data = CompanyCategroy::find()->select(["name"])->where(["id"=>$model->company_id])->one();
        $model_data = CompanyInterfaceModule::find()->select(["*"])->where(["id"=>$model->module_id])->one();
?>
    <p>
        <?= Html::a('修改', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('删除', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => '你确定删除这条数据吗?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
            'label'=>'所属企业',
            'format'=>'raw',
            'value' => $company_data['name']
            ],
            'url:url',
            [
            'label'=>'公钥',
            'format'=>'raw',
            'value'=>$model->public_key
            ],
            [
            'label'=>'秘钥',
            'format'=>'raw',
            'value'=>$model->privace_key
            ],
            'module_id',
            [  
            'label'=>'所属模块',
            'format'=>'raw',
            'value'=>$model_data['module_name']
            ],
            [
                'label'=>'创建时间',
                'format'=>['date', 'php:Y-m-d'],
                'value' => $model->createtime
             ],
            [  
            'label'=>'安全协议',
            'format'=>'raw',
            'value'=>$model->protocol
            ],
        ],
    ]) ?>

</div>
