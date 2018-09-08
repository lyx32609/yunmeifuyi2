<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Petition */
$type = 3;
$this->title = '签呈发布';
$this->params['breadcrumbs'][] = ['label' => '签呈管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>


<div class="petition-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <form action="/petition/test-upload" id="signForm" enctype="multipart/form-data" method="post">

            <input name="_csrf-backend" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken?>" >
            <input type="file" name="master_img"   multiple/>
            <input type="submit" value="提交"/>
    </form>

               