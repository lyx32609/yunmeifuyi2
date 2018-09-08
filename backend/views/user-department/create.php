<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\UserDepartment */

$this->title = Yii::t('app', 'Create').' ' .Yii::t('app','部门');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'User Departments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-department-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
