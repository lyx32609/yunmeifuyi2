<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\UserSupplier */

$this->title = Yii::t('app', 'Create').' '.Yii::t('app','User Supplier');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'User Suppliers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-supplier-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
