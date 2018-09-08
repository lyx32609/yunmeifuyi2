<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Shop */

$this->title = $model->shop_name;
$this->params['breadcrumbs'][] = ['label' => '客户管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shop-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'shop_name',
            'name',
            'phone',
            'shop_type',
            'shop_source',
            'shop_status',
            'shop_priority',
            'shop_longitude',
            'shop_latitude',
            'shop_image',
            'user_name',
            'user_id',
            'company_category_id',
            'shop_review',
            'shop_addr',
            'shop_domain',
            'createtime',
            'shop_title',
            'shop_describe',
        ],
    ]) ?>

</div>
