<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\CompanyCategroy */

$this->title = 'Create Company Categroy';
$this->params['breadcrumbs'][] = ['label' => 'Company Categroys', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-categroy-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
