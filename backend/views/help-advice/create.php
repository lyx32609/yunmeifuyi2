<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\HelpAdvice */

$this->title = 'Create Help Advice';
$this->params['breadcrumbs'][] = ['label' => 'Help Advices', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="help-advice-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
