<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\WithdSetting */

$this->title = 'Create Withd Setting';
$this->params['breadcrumbs'][] = ['label' => 'Withd Settings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withd-setting-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
