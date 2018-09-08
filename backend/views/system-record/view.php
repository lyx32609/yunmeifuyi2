<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\SystemRecord */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '系统记录', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-record-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'staff_num',
            'content',
            'type',
            'brand_model',
            [
                'label' => '日期时间',
                'value' => date("Y-m-d H:i:s", $model->time),
            ],

        ],
    ]) ?>

</div>
