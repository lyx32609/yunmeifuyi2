<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\problem */

$this->title = 'Update Problem: ' . $model->problem_id;
$this->params['breadcrumbs'][] = ['label' => 'Problems', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->problem_id, 'url' => ['view', 'id' => $model->problem_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="problem-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
