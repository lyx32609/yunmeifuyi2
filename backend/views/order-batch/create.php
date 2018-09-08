<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\OrderBatch */

$this->title = Yii::t('app', '新建车次');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', '车次详情'), 'url' => ['index']];
$this->params['breadcrumbs'][] = "新建车次";
?>
<div class="order-batch-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
