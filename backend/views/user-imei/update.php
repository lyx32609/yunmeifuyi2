<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\PutImei */

$this->title = 'Update Put Imei: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '设备', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="put-imei-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
