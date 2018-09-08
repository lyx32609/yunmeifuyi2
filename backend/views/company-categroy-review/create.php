<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\CompanyCategroyReview */

$this->title = 'Create Company Categroy Review';
$this->params['breadcrumbs'][] = ['label' => 'Company Categroy Reviews', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-categroy-review-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_create', [
        'model' => $model,
        'model_company' => $model_company,
        //'model_company' => $model_company,
    ]) ?>

</div>
