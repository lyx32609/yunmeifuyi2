<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\PutImei;
use backend\models\CompanyCategroy;
use backend\models\UserDepartment;
use backend\models\User;

/* @var $this yii\web\View */
/* @var $model backend\models\PutImei */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => '设备变更管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="put-imei-view">
    <?php
    $data = PutImei::find()
        ->where(['user_id' => $model->user_id, 'status' => '2'])
        ->orderBy('id desc')
        ->asArray()
        ->all();
    $user = User::find()
        ->where(['id' => $model->user_id])
        ->asArray()
        ->one();
    $company = CompanyCategroy::find()
        ->where(['id' => $model->company_categroy_id])
        ->asArray()
        ->one();
    $department = UserDepartment::find()
        ->where(['id' => $model->department_id])
        ->asArray()
        ->one();
    ?>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>姓名</th>
            <th>所属公司</th>
            <th>所属部门</th>
            <th>新设备</th>
            <th>提报时间</th>
            <th>变更时间</th>
            <th>旧设备</th>

        </tr>
        </thead>
        <tbody>

        <?php
        foreach ($data as $k => $v) {
            ?>

            <tr data-key="<?php echo $v['id'] ?>">
                <td><?php echo $k + 1 ?></td>
                <td><?php echo $user['name'] ?></td>
                <td><?php echo $company['name'] ?></td>
                <td><?php echo $department['name'] ?></td>
                <td><?php echo $v['new_brand'] ?></td>
                <td><?php echo date('Y-m-d H:i:s', $v['submit_time']) ?></td>
                <td><?php echo date('Y-m-d H:i:s', $v['pass_time']) ?></td>
                <td><?php echo $v['old_brand'] ?></td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>


</div>
