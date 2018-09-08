<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\WithdrawRecord */

$this->title = 'Update Withdraw Record: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Withdraw Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="withdraw-record-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
