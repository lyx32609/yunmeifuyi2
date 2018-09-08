<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\OrderDelivery */

$this->title = Yii::t('app', '修改 {modelClass}: ', [
    'modelClass' => '发车详情',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '发车详情'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="order-delivery-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
