<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\IosVersion */

$this->title = 'Create Ios Version';
$this->params['breadcrumbs'][] = ['label' => 'Ios Versions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ios-version-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
