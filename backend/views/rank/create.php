<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\UserIndex */

$this->title = 'Create User Index';
$this->params['breadcrumbs'][] = ['label' => 'User Indices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
