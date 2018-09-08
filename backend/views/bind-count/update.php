<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\BindCount */

$this->title = 'Update Bind Count: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Bind Counts', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="bind-count-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
