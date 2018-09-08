<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Help */

$this->title = '创建三级分类';
$this->params['breadcrumbs'][] = ['label' => '使用须知', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="help-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form2', [
        'model' => $model,
    ]) ?>

</div>
