<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Banner */

$this->params['breadcrumbs'][] = ['label' => '首页图片', 'url' => ['index']];
$this->params['breadcrumbs'][] = '修改';
?>
<div class="banner-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
