<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\WithdRate;

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
  .button_open{background-color:red;}
  .button_close{background-color:green;}
  }
</style>
<script type="text/javascript">
    function is_confirm($parm)
    {
        // if(confirm($parm)){
        //     document.getElementById("form1").submit();
        // }
        document.getElementById("form1").submit($parm);
    }
</script>
<div class="withd-rate-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <form action="set" method="get" id="form1" >
        <div class="row rowMargin"> 
            <div class="row2">
                <input type="radio" name="is_open_which" value="money" class="change"/>
                <span class="change">提现手续费：每笔</span>
                <input type="text" name="pound_money[]" value="<?php {echo $rate_data['pound_money'];}?>" class="change"/><span class="change">元 </span> 
                <button name="pound_money[]" value='update_money' onclick="is_confirm('确认修改单笔手续费吗？')" class="change">修改</button>
            </div>
            <div class="row2">
                <input type="radio" name="is_open_which" value="percent" class="change"/>
                <span class="change">提现手续费：提现金额</span>
                <input type="text" name="pound_percent[]" class="change" value="<?php {echo $rate_data['pound_percent'];}?>"/><span class="change">%</span>
                <button name="pound_percent[]" value='update_percent' onclick="is_confirm('确认修改提现百分比吗？')" class="change">修改</button>
            </div>
            <div class="row2">
                <input type="hidden"  value="1" />
                <button name="is_open" value="1" onclick="is_confirm('确认开启收手续费吗？')" class="change <?php if($rate_data['is_open'] == 1){?>button_back<?php }?>" >开启</button>
                <button  name="is_open" value="2" onclick="is_confirm('确认关闭收手续费吗？')" class="change"/>关闭</button>
            </div>
        </div>
    </form>
    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Withd Rate', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'pound_money',
            'pound_percent',
            'is_open',
            'is_open_which',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
