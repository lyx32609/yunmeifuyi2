<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\CompanyCategroy */

$this->title = '创建子公司';
$this->params['breadcrumbs'][] = ['label' => '子公司列表', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-categroy-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
