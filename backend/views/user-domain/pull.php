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
	
    <form action='/user-domain/pull-agent' method='post'>
    	<input type="submit" name='tongbu' class='btn btn-success' value='同步集采地区'>
    </form>
</div>