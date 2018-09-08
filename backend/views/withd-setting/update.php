<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\WithdSetting */

$this->title = 'Update Withd Setting: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Withd Settings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="withd-setting-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
