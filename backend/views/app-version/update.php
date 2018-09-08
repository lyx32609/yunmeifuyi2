<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\AppVersion */

$this->title =Yii::t('app', 'Update App Version'); 
//$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Update App Version'), 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = '修改查看';
?>
<div class="app-version-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
