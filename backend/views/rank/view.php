<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\UserIndex */


?>
<div class="user-index-view">

<section class="content" style="overflow: visible;">
<!-- <ul class="breadcrumb"><li><a href="index">首页</a></li>
<li><a href="/user-sign/index">用户签到</a></li>
<li class="active">116005</li>
</ul>         -->            

<div class="user-sign-view">
			<h1><?php echo $_GET['id']; ?></h1>
			<table id="w0" class="table table-striped table-bordered detail-view">
				<tbody>
					<tr>
						<th>指标</th>
						<td>排名</td>
						<td>数量</td>
					</tr>
				<?php foreach ($model as $v){?>
					<tr>
						<th><?php echo $v['typeName']?></th>
						<td><?php echo $v['rank']?></td>
						<td><?php echo $v['num']?></td>
					</tr>
				<?php }?>
				</tbody>
			</table>
		</div>
                </section>




</div>
