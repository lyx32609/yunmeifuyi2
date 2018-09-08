<?php

use yii\helpers\Html;
use backend\models\PutImei;
use backend\models\User;
use components\helpers\DateHelper;


/* @var $this yii\web\View */
/* @var $searchModel backend\models\PutImeiModel */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '设备变更管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="put-imei-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php
    if (Yii::$app->request->get('select')) {
        if (!empty(Yii::$app->request->post())) {
            $data = \Yii::$app->request->post('PutImeiSearch');
        } else {
            $data = \Yii::$app->request->get('PutImeiSearch');
        }
        $times = $data['times'];
        $area = $data['area'];
        $city = $data['city'];
        $username = $data['username'];
        $name = $data['name'];
        $company_id = $data['company_categroy_id'];
        $company_id = empty($data['company_categroy_id']) ? "" : $data['company_categroy_id'];
        $department_id = empty($data['department']) ? "" : $data['department'];
        $start_time = !empty($data['start_time']) ? strtotime($data['start_time']) : DateHelper::getDayStartTime(7);
        $end_time = !empty($data['end_time']) ? (strtotime($data['end_time']) + 86399) : DateHelper::getTodayEndTime();

        $sql = 'SELECT count(*) AS num,p.id,p.`user_id`,p.`new_brand`,p.`pass_time`,u.`name`,d.`name` department_name,c.`name`company_name,p.`submit_time` FROM';
        $sql .= '(SELECT*	FROM off_put_imei	';

        if (!empty($start_time) && !empty($end_time)) {
            $sql .= " where pass_time between " . "$start_time " . "and " . $end_time;
        }
        $sql .= ' and `status` = 2 ';
        if (!empty($company_id) && $company_id != '请选择公司') {
            $sql .= " AND company_categroy_id = " . "$company_id ";
        }
        if (!empty($department_id) && $department_id != '请选择部门' && $department_id != '暂无部门') {
            $sql .= " AND department_id = " . "$department_id ";
        }
        //根据账号查询
        if (!empty($username)) {
            //查询用户ID
            $user_data = User::find()
                ->select(['id'])
                ->where(['username' => $username])
                ->asArray()
                ->one();
            if ($user_data) {
                $user_id = $user_data['id'];
                $sql .= " AND user_id = " . "$user_id";
            } else {
                echo "<script>alert('查询的账号不存在，请输入正确账号');</script>";
            }

        }
        //根据用户名查询
        if (!empty($name)) {
            //查询用户ID
            $user_data = User::find()
                ->select(['id'])
                ->where(['name' => $name])
                ->asArray()
                ->one();
            if ($user_data) {
                $user_id = $user_data['id'];
                $sql .= " AND user_id = " . "$user_id";
            } else {
                echo "<script>alert('输入用户名不存在，请输入正确的用户名');</script>";
            }

        }

        $sql .= ' ORDER BY id DESC) AS p 
                    LEFT JOIN off_user u ON p.user_id = u.id 
                    LEFT JOIN off_user_department d ON d.id = p.department_id
                    LEFT JOIN off_company_categroy c ON c.id = p.company_categroy_id 
                    GROUP BY p.user_id	';
        if (!empty($times) && $times != '请选择更换次数') {
            $sql .= " having num = " . $times;
        }
        $sql .= ' ORDER BY p.id DESC';
        $data = PutImei::findBySql($sql)
            ->asArray()
            ->all();
    } else {
        $sql = 'SELECT count(*) AS num,	p.id,	p.`user_id`,	p.`new_brand`,	p.`pass_time`,	u.`name`,	d.`name` department_name,	
            c.`name` company_name ,p.submit_time FROM(SELECT*	FROM off_put_imei	WHERE`status` = 2	ORDER BY id DESC	) AS p 
            LEFT JOIN off_user u ON p.user_id = u.id LEFT JOIN off_user_department d ON d.id = p.department_id
            LEFT JOIN off_company_categroy c ON c.id = p.company_categroy_id GROUP BY	p.user_id	ORDER BY p.id DESC';
        $data = PutImei::findBySql($sql)
            ->asArray()
            ->all();
    }


    ?>
    <table class="table table-striped table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>姓名</th>
            <th>所属公司</th>
            <th>所属部门</th>
            <th>设备信息</th>
            <th>变更次数</th>
            <th>提报时间</th>
            <th>变更时间</th>
            <th>详情</th>
        </tr>
        </thead>
        <tbody>

        <?php
        if (!$data) {
            echo "<script>alert('该条件下查询暂无数据');</script>";
        }
        foreach ($data as $k => $v) {
            ?>

            <tr data-key="<?php echo $v['id'] ?>">
                <td><?php echo $k + 1 ?></td>
                <td><?php echo $v['name'] ?></td>
                <td><?php echo $v['company_name'] ?></td>
                <td><?php echo $v['department_name'] ?></td>
                <td><?php echo $v['new_brand'] ?></td>
                <td><?php echo $v['num'] ?></td>
                <td><?php echo date('Y-m-d H:i:s', $v['submit_time'])  ?></td>
                <td><?php echo date('Y-m-d H:i:s', $v['pass_time']) ?></td>
                <td>
                    <a href="/user-imei/view?id=<?php echo $v['id'] ?>" title="查看" aria-label="查看" data-pjax="0">
                        <span class="glyphicon glyphicon-eye-open"></span>
                </td>
            </tr>
            <?php
        }
        ?>
        </tbody>
    </table>
</div>
