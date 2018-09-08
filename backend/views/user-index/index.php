<?php
use yii\bootstrap\Alert;
?>
<div class="user-group-form">
    <?php if(Yii::$app->session->hasFlash('success')):

               echo Alert::widget([
                  'options' => ['class' => 'alert-info'],
                  'body' => Yii::$app->session->getFlash('success'),
               ]);
                
               endif;?>
	    	请耐心等待一下,如果不填写日期则默认计算昨日指标
    <form action='/user-index/synchronization'   method='post'>

    	开始时间：<input type="text"  name='stime'  value=''> 例子：2017-4-27<br>
    	结束时间：<input type="text"  name='etime'  value=''> 例子 : 2017-5-1<br>
    	<input type="submit" name='tongbu' class='btn btn-success' value='计算可统计部门下员工指标'>
    </form>
</div>