<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\CompanyCategroy */

$this->title = '企业注册审核: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => '企业注册审核', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="company-categroy-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_check', [
        'model' => $model,
    ]) ?>

</div>
