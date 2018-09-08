<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\PutImei */

$this->title = 'Create Put Imei';
$this->params['breadcrumbs'][] = ['label' => 'Put Imeis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="put-imei-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
