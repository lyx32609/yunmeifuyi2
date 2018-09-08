<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\User;
/* @var $this yii\web\View */
/* @var $model backend\models\Petition */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => '签呈管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="petition-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php //= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php //= Html::a('Delete', ['delete', 'id' => $model->id], [
//            'class' => 'btn btn-danger',
//            'data' => [
//                'confirm' => 'Are you sure you want to delete this item?',
//                'method' => 'post',
//            ],
       // ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'label'=>'签呈标题',
                'value'=>$model->title,
            ],
            [
                'label'=>'签呈内容',
                'value'=>$model->content,
            ],
        ],
        'template' => '<tr><th width="100px">{label}</th><td>{value}</td></tr>',
        'options' => ['class' => 'table table-striped table-bordered detail-view'],
    ]);
    echo "<table id='w0' class='table table-striped table-bordered detail-view'>";
    foreach (Findids($model) as $value){
        echo '<tr>';
            echo '<th>';
            echo '审核人：' . $value['name'];
            echo '</th>';
            echo '<th>';
            echo '部门：' .$value['cname'] . $value['dname'];
            echo '</th>';
            echo '<th>';
            echo '审核时间：' . $value['examine_time'];
            echo '</th>';
        echo '</tr>';
        echo '<tr>';
            echo '<th>';
            echo '状态：' . $value['status'];
            echo '</th>';
            echo '<th>';
            echo '审核意见：' . $value['advice'];
            echo '</th>';
        echo '</tr>';
    }
    echo '</table>';

    function Finduser($model){
        $people = User::find()
            ->select(["name"])
            ->where(["id" => $model->uid])
            ->asArray()
            ->one();
        return $people["name"];
    }
    function Findids($model){
        $ids = explode(',',$model->ids);
        foreach ($ids as $value){
            $people = User::find()
                ->where(["id" => $value])
                ->asArray()
                ->one();
            $department = \backend\models\UserDepartment::find()
                ->select('name as dname')
                ->where(['id'=>$people['department_id']])
                ->asArray()
                ->one();
            $passtime = \backend\models\Examine::find()
                ->where(['petition_id'=> $model->id])
                ->andWhere(['uid'=>$value])
                ->one();
            $company = \backend\models\CompanyCategroy::find()
                ->select('name as cname')
                ->where(['id'=>$people['company_categroy_id']])
                ->asArray()
                ->one();
            $a['name'] = $people['name'];
            $a['dname'] = $department['dname'];
            $a['examine_time'] = datetime($passtime);
            $a['cname'] = $company['cname'];
            $a['advice'] = $passtime['advice'];
            $a['status'] = change($passtime);
            $b[] = $a ;
        }
        return $b;
    }
    function datetime($model){
        if (!empty($model->examine_time)){
            return date('Y-m-d H:i:s', $model->examine_time);

        }
    }
    //0：不同意 1：同意 2：待审
    function change($model){
        if ($model->status == 1){
            return '同意';
        }elseif ($model->status == 0){
            return '不同意';
        }elseif ($model->status == 2){
            return '审核中';
        }
    }

    ?>
</div>
