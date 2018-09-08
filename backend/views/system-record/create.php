<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\SystemRecord */

$this->title = 'Create System Record';
$this->params['breadcrumbs'][] = ['label' => 'System Records', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-record-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
