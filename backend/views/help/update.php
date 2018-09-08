<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Help */

$this->title = '修改: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '使用须知', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="help-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form2', [
        'model' => $model,
    ]) ?>

</div>
