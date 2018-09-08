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
	    	请耐心等待一下
    <form action='/user-location/synchronization' method='post'>

    	同步数量：<input type="text"  name='num'  value='1000'><br>
    	<input type="submit" name='tongbu' class='btn btn-success' value='同步员工定位记录数据'>
    </form>
</div>