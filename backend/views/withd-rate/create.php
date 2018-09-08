<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\WithdRate */

$this->title = 'Create Withd Rate';
$this->params['breadcrumbs'][] = ['label' => 'Withd Rates', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withd-rate-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
