<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\assets\SwitchInput;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $searchModel backend\models\CompanyReviewSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('app', 'Company Reviews');
$this->params['breadcrumbs'][] = $this->title;
?>
<script>
function changeReview(){
    var review=$('#review').val();
    $.ajax({
        type: "GET",
        url: "/system/update?review="+review,
        async:false,
        success: function(data){
        	//$("select#userlocationsearch-department").html(data);
        }
    });
} 
</script>
<div class="company-review-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php // echo Html::a('Create Company Review', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php // GridView::widget([
     //   'dataProvider' => $dataProvider,
      //  'filterModel' => $searchModel,
       // 'columns' => [
          //  ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'review',

           // ['class' => 'yii\grid\ActionColumn'],
     //   ],
   // ]); ?>
    
    
</div>

<div class="company-review-view">
<table id="w0" class="table table-striped table-bordered detail-view"><tbody>
<tr><th>是否开启企业注册审核</th><td>

<input type="checkbox" 
<?php if($review == 1){?>
checked="checked"
<?php } ?>
value="<?php echo $review;?>"
onchange="changeReview();"
id = "review"
>


<?php // $form = ActiveForm::begin(); ?>
<?php // $form->field($searchModel,'review')->widget(SwitchInput::className())->label('企业注册审核开关') ?>
<?php // ActiveForm::end(); ?>

</td>
</tr>

    <tr>
        <th>IOS版本信息</th>
        <td>
        	<a class="btn btn-xs btn-success" href="/ios-version/update?id=1" title="查看"><span class="glyphicon fa fa-eye"></span></a>
        </td>
    </tr>
    <tr>
        <th>安卓版本信息</th>
        <td>
        	<a class="btn btn-xs btn-success" href="/app-version/update?id=1" title="查看"><span class="glyphicon fa fa-eye"></span></a>
        </td>
    </tr>
</tbody>
</table>

</div>	

</body>
