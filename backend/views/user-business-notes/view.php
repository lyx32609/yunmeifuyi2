<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\User;
use backend\models\UserBusiness;

/* @var $this yii\web\View */
/* @var $model backend\models\UserBusinessNotes */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '跟进记录', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

if (!empty($model->business_id)) {
    $userdata = UserBusiness::find()->select(["customer_name"])->where(["id" => $model->business_id])->one();
    $customer_name = $userdata["customer_name"];//客户名称
}
if (!empty($model->staff_num)) {
    $userdata = User::find()->select(["name"])->where(["username" => $model->staff_num])->one();
    $name =  $userdata["name"];//姓名
}
?>
<div class="user-business-notes-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'id',
//            'business_id',
            [
                'label' => '客户名称',
                'value' => $customer_name,
            ],
            [
                'label' => '账号',
                'value' => $model->staff_num,
            ],
            [
                'label' => '姓名',
                'value' => $name,
            ],
            [
                'label' => '提交内容',
                'value' => $model->followup_text,
            ],
            [
                'label' => '日期时间',
                'value' => date("Y-m-d H:i:s", $model->time),
            ],
//            'staff_num',
//            'time',
//            'followup_text',
        ],
    ]) ?>

</div>
