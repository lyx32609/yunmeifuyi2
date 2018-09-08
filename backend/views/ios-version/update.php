<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\IosVersion */

$this->title = Yii::t('app', 'Update Ios Version'); 
//$this->params['breadcrumbs'][] = ['label' => 'Ios Versions', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = '修改';
?>
<div class="ios-version-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
