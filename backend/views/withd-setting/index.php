<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\WithdRate;
use backend\models\User;
use backend\models\UserDepartment;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\WithdRateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '提现费率';
$this->params['breadcrumbs'][] = "$this->title";
$rate_data = WithdRate::find()->asArray()->one();
?>
<style>
   .change{float: left;margin:5px;}
  .rowMargin{margin-left: 10%;}
  .row2{float: left;width:30%;}
  }

  }
</style>
<script type="text/javascript">
    function is_go($parm)
    {
        // if(confirm($parm))
        // {
            document.getElementById("form1").submit();
        // }

    }
</script>
<div class="withd-setting-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <form action="/withd-rate/set" method="get" id="form1" >
        <div class="row rowMargin"> 
            <div class="row2">
                <input type="radio" <?php if($rate_data['is_open_which'] == "money"){?>checked='checked' <?php }?> name="is_open_which" value="money" class="change"/>
                <span class="change">提现手续费：每笔</span>
                <input type="text" name="pound_money[]" value="<?php {echo $rate_data['pound_money'];}?>" class="change"/><span class="change">元 </span> 
                <button name="pound_money[]" value='update_money' onclick="is_go('确认修改单笔手续费吗？')" class="change btn btn-info">修改</button>
            </div>
            <div class="row2">
                <input type="radio" <?php if($rate_data['is_open_which'] == "percent"){?>checked='checked' <?php }?> name="is_open_which" value="percent" class="change"/>
                <span class="change">提现手续费：提现金额</span>
                <input type="text" name="pound_percent[]" class="change" value="<?php {echo $rate_data['pound_percent'];}?>"/><span class="change">%</span>
                <button name="pound_percent[]" value='update_percent' onclick="is_go('确认修改提现百分比吗？')" class="change btn btn-info">修改</button>
            </div>
                 <input type="hidden"  value="1" />
                <button  name="is_open[]" value="1" onclick="is_go('确认开启收手续费吗？')" class="change btn <?php if($rate_data['is_open'] == 1){?> btn-success<?php }else{?>btn-danger<?php }?>" >开启</button>
                <button  name="is_open[]" value="2" onclick="is_go('确认关闭收手续费吗？')" class="change btn <?php if($rate_data['is_open'] == 2){?> btn-success<?php }else{?>btn-danger<?php }?>"/>关闭</button>
                <input type="hidden" name="is_open[]" value=""/>
            </div>
        </div>
        <div class="row rowMargin">
            <div class="row2">
                <span class="change">最低可转出的金额：</span>
                <input type="text" name="transferable_out_money[]" value="<?php {echo $rate_data['transferable_out_money'];}?>" class="change"/><span class="change">元 </span> 
                <button name="transferable_out_money[]" value='update_out_money' onclick="is_go('确认修改最低可转出金额吗？')" class="change btn btn-info">修改</button>
            </div>
            <div class="row2">
                 <input type="hidden"  value="1" />
                <button  name="is_open_transferable_out[]" value="1" onclick="is_go('确认开启最低可转出金额吗？')" class="change btn <?php if($rate_data['is_open_transferable_out'] == 1){?> btn-success<?php }else{?>btn-danger<?php }?>" >开启</button>
                <button  name="is_open_transferable_out[]" value="2" onclick="is_go('确认关闭最低可转出金额吗？')" class="change btn <?php if($rate_data['is_open_transferable_out'] == 2){?> btn-success<?php }else{?>btn-danger<?php }?>"/>关闭</button>
                <input type="hidden" name="is_open_transferable_out[]" value=""/>
            </div>
        </div>
    </form>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>


    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'label'=>'修改人账号',
                'format'=>'raw',
                'value' => function($model){
                    $data = User::find()->where(['id'=>$model->set_uid])->asArray()->one();
                    return $data['username'];
                }
            ],
            [
                'label'=>'修改人姓名',
                'format'=>'raw',
                'value' => function($model){
                    $data = User::find()->where(['id'=>$model->set_uid])->asArray()->one();
                    return $data['name'];
                }
            ],
            [
                'label'=>'修改人部门',
                'format'=>'raw',
                'value' => function($model){
                    $data = UserDepartment::find()->where(['id'=>$model->set_department_id])->asArray()->one();
                    return $data['name'];
                }
            ],
            'set_cont',
            'set_before',
            'set_after',   
            [
                'label'=>'修改时间',
                'format'=>'raw',
                'value' => function($model){
                    return Date("Y-m-d H:i:s",$model->set_time);
                }
            ], 
        ],
        'pager'=>[
                    'firstPageLabel'=>"首页",
                       'prevPageLabel'=>'上一页',     
                    'nextPageLabel'=>'下一页',
                    'lastPageLabel'=>'尾页',
                ],
    ]); ?>
</div>
