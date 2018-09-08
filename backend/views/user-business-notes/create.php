<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\UserBusinessNotes */

$this->title = 'Create User Business Notes';
$this->params['breadcrumbs'][] = ['label' => 'User Business Notes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-business-notes-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
