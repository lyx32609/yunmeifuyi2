<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\PercentumSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '提成设置';
$this->params['breadcrumbs'][] = $this->title;
?>

<script type="text/javascript">
    function disp_confirm()
    {
        if(confirm('确认修改提成比例？')){
            document.getElementById("formid").submit();
        }
    }
    function day_confirm()
    {
        if(confirm('确认修改时间？')){
            document.getElementById("dayid").submit();
        }
    }

</script>
<div class="percentum-index">
    <h2>
        <?= Html::encode($this->title)?></h2>
        <form action="deal"  method="get" id="formid" style="float: left;margin-left: 60px;">
            <span style="font-size: 16px">
                提成比例:
                <?php if (empty($new_per)){?>
                    金额 <input type="text" name="new_per" value="" style="width: 100px;">%
                <?php } else {?>
                    金额 <input type="text" name="new_per" value="<?php echo $new_per->new_per?>" style="width: 100px;">%
                <?php }?>
            </span>
            <input type="button" onclick="disp_confirm()" class="btn btn-success" value="修改">
            <p style="font-size: 14px;color: red;">*自动筛选已完成且过了售后期的订单</p>
        </form>

    <div style="float: left">
        <?php if (!empty($new_per)){?>
        <?php if ($new_per->is_open == 1){?>
            <div style="display:inline;margin-left:30px;">
                <?php //= Html::submitButton('关闭', ['class' => 'btn btn-success', 'name' => 'close', 'value' => '2']) ?>
                <button id="close1" name="close" value="2" class="btn btn-success">关闭</button>
            </div>
        <?php } else {?>
            <div style="display:inline;margin-left:30px;">
                <?php //= Html::submitButton('开启', ['class' => 'btn btn-success', 'name' => 'open', 'value' => '1']) ?>
                <button id="open" name="open" value="1" class="btn btn-success">开启</button>
            </div>
        <?php }?>
        <?php }?>
    </div>
    <form action="day-deal" method="get" id="dayid" >
        <?php if (!empty($new_per)){?>
            <span style="font-size: 16px; margin-left: 100px;">
                订单售后期 <input type="text" name="day" value="<?php echo $new_per->confirm_day?>" style="width: 100px;">天
            </span>
            <input type="button" onclick="day_confirm()" class="btn btn-success" value="修改">
        <?php } ?>
    </form>
    <div style="clear: both"></div>

    <?php  echo $this->render('_search', ['model' => $searchModel, 'new_per'=>$new_per]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'username',
            'name',
            'content',
            ['label'=>"部门",
                "value"=>function($model)
                {
                    $user = \backend\models\UserDepartment::find()->where(['id'=>$model->department_id])->asArray()->one();
                    return $user['name'];
                }
            ],
            ['label'=>"修改前比例",
                "value"=>function($model)
                {
                   return $model->old_per . '%';
                }
            ],
            ['label'=>"修改后比例",
                "value"=>function($model)
                {
                    return $model->new_per . '%';
                }
            ],
            ['label'=>"修改时间",
                'attribute' => 'time',
                'format' => ['date', 'php:Y-m-d H:i:s'],
            ],
        ],
    ]); ?>
</div>
<script>
    $('#close1').click(function () {
       var value = $('#close1').val();
        $.get('/percentum/index',{'close':value},function (data) {
            var message = JSON.parse(data);
            if(message.msg == '1'){
                alert('关闭成功！');location.reload();
            }else if (message.msg == '2'){
                alert('关闭失败！');location.reload();
            }
        })
    })
    $('#open').click(function () {
       var value =  $('#open').val();
        $.get('/percentum/index',{'open':value},function (data) {
            var message = JSON.parse(data);
            if(message.msg == '1'){
                alert('开启成功！');location.reload();
            }else if (message.msg == '2'){
                alert('开启失败！');location.reload();
            }
        })
    })
</script>
