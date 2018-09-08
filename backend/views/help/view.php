<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\Help;

/* @var $this yii\web\View */
/* @var $model backend\models\Help */

$data = Help::find()
    ->where(['id'=>$model->son_id])
    ->select(['content'])
    ->asArray()
    ->one();
$res = Help::find()
    ->where(['id'=>$model->parent_id])
    ->select(['content'])
    ->asArray()
    ->one();
$son_id = $data['content'];
$parent_id = $res['content'];

$this->title = $son_id;
$this->params['breadcrumbs'][] = ['label' => '使用须知', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="help-view">

    <h1><?= Html::encode($this->title) ?></h1>
<!--
    <p>
        <?/*= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) */?>
        <?/*= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) */?>
    </p>-->


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'type',
//            'parent_id',
//            'son_id',
            [
                'label'=>'parent_id',
                'format'=>'raw',
                'value' => $parent_id,
            ],[
                'label'=>'son_id',
                'format'=>'raw',
                'value' => $son_id,
            ],
            'content',
            'sumup',
            'sumdown',
        ],
    ]) ?>

</div>
