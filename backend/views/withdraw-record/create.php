<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\WithdrawRecord */

$this->title = 'Create Withdraw Record';
$this->params['breadcrumbs'][] = ['label' => 'Withdraw Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withdraw-record-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
