<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\BindCount */

$this->title = 'Create Bind Count';
$this->params['breadcrumbs'][] = ['label' => 'Bind Counts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bind-count-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
