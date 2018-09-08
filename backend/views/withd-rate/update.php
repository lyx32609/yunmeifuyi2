<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\WithdRate */

$this->title = 'Update Withd Rate: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Withd Rates', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="withd-rate-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
