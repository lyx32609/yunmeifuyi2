<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\UserLocation */

$this->title = 'Create User Location';
$this->params['breadcrumbs'][] = ['label' => 'User Locations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-location-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
