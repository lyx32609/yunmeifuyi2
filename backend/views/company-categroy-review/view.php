<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\CompanyCategroyReview */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Company Categroy Reviews', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-categroy-review-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'status',
            'createtime:datetime',
            'phone',
            'area_id',
            'domain_id',
            'fly',
            'type',
            'review',
            'license_num',
            'register_money',
            'business',
            'business_ress',
            'staff_num',
            'acting',
            'proxy_level',
            'service_area',
            'distribution_merchant',
            'distribution_car',
            'distribution_staff',
            'goods_num',
            'failure',
            'goods_type',
            'service_type',
            'product_type',
            'salas_business',
            'license_image',
            'user_image_negative',
            'user_image_positive',
        ],
    ]) ?>

</div>
