<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\ShopNote */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Shop Note',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Shop Notes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="shop-note-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
