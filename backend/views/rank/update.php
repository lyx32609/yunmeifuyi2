<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\UserIndex */

$this->title = 'Update User Index: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Indices', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-index-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
