<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use backend\models\Regions;
use app\models\CompanyGoods;
use app\models\CompanyService;
use app\models\CompanyProduct;

/* @var $this yii\web\View */
/* @var $model backend\models\CompanyCategroy */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="company-categroy-form">
<form method='post' action="/company/save-check?id=<?php echo $model->id;?>">
<table id="w0" class="table table-striped table-bordered detail-view">
    <tbody>
    <tr><th width="10%">名称</th><td width="10%">填写值</td><td>审核理由</td></tr>
    
    <tr><th></th><td></td><td></td></tr>
        <?php //$form = ActiveForm::begin(); ?>
    
    <tr><th>企业名称</th><td><?php echo $model->name;?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[name]" value=""></td></tr>
    <tr><th>企业类型</th><td>
			<?php                 
                if($model->status == 0){echo "运营商";}
                if($model->status == 1){echo "销售商";}
                if($model->status == 2){echo "供货商";}
                if($model->status == 3){echo "配送商";}
                if($model->status == 4){echo "生产商";}
                if($model->status == 5){echo "服务商";}
            ?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[status]" value=""></td></tr>
    <tr><th>创建日期</th><td><?php echo date('Y-m-d',$model->createtime) ?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[createtime]" value=""></td></tr>
    <tr><th>联系方式</th><td><?php echo $model->phone ?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[phone]" value=""></td></tr>
    <tr><th>省份</th><td><?php $area_data = Regions::find($model->area_id)->select(['local_name'])->where(["region_id"=>$model->area_id])->one();echo $area_data['local_name'];?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[area_id]" value=""></td></tr>
    <tr><th>地区</th><td><?php $city_data = Regions::find($model->domain_id)->select(['local_name'])->where(["region_id"=>$model->domain_id])->one();echo $city_data['local_name'];?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[domain_id]" value=""></td></tr>
    <tr><th>是否主公司</th><td><?php echo $model->fly == 0 ? '主公司' : '非主公司';  ?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[fly]" value=""></td></tr>
    <tr><th>与云媒合作</th><td>
    <?php                 
                if($model->type == 0){echo "未合作";}
                if($model->type == 1){echo "已合作";}
            ?>
    </td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[type]" value=""></td></tr>
<!--     <tr><th>审核状态</th><td>
        <?php                 
                if($model->review == 0){echo "待审核";}
                if($model->review == 1){echo "审核中";}
                if($model->review == 2){echo "审核通过";}
                if($model->review == 3){echo "审核未通过";}
            ?>
    </td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[review]" value=""></td></tr> -->
    <tr><th>执照编号</th><td><?php echo $model->license_num ?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[license_num]" value=""></td></tr>
    <tr><th>注册资金</th><td><?php echo $model->register_money ?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[register_money]" value=""></td></tr>
    <tr><th>经营面积</th><td><?php echo $model->business ?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[business]" value=""></td></tr>
    <tr><th>经营地址</th><td><?php echo $model->business_ress ?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[business_ress]" value=""></td></tr>
    <tr><th>人员数量</th><td><?php echo $model->staff_num ?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[staff_num]" value=""></td></tr>
    <tr><th>代理品牌</th><td><?php echo $model->acting ?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[acting]" value=""></td></tr>
    <tr><th>代理级别</th><td><?php echo $model->proxy_level ?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[proxy_level]" value=""></td></tr>
    <tr><th>服务区域</th><td><?php echo $model->service_area ?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[service_area]" value=""></td></tr>
    <tr><th>配送商户</th><td><?php echo $model->distribution_merchant ?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[distribution_merchant]" value=""></td></tr>
    <tr><th>配送车辆</th><td><?php echo $model->distribution_car ?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[distribution_car]" value=""></td></tr>
    <tr><th>配送人员</th><td><?php echo $model->distribution_staff ?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[distribution_staff]" value=""></td></tr>
    <tr><th>商品数量</th><td><?php echo $model->goods_num ?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[goods_num]" value=""></td></tr>
    <tr><th>账号状态</th><td>
                    <?php 
                        if($model->failure == 0){echo "永久使用";}
                        if($model->failure == 1){echo "试用";} 
                    ?></td><td><input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[failure]" value=""></td></tr>
    <tr><th>商品类型</th><td><?php $goods = CompanyGoods::find()->select(["goods_name"])->where(["id"=>$model->goods_type])->one();echo $goods['goods_name'];?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[goods_type]" value=""></td></tr>
    <tr><th>服务类型</th><td><?php $service = CompanyService::find()->select(["service_name"])->where(["id"=>$model->service_type])->one();echo $service['service_name']; ?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[service_type]" value=""></td></tr>
    <tr><th>产品类型</th><td><?php $product = CompanyProduct::find()->select(["product_name"])->where(["id"=>$model->product_type])->one();echo $product["product_name"]; ?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[product_type]" value=""></td></tr>
    <tr><th>服务区域</th><td><?php echo $model->salas_business ?></td><td>   <input type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[salas_business]" value=""></td></tr>
    <tr><th>营业执照照片</th><td>   <input style="width:250%;margin-bottom:10px;" type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[license_image]" value=""/><img class="imgBtn" src="<?php echo $model->license_image ?>" style="width:300px;height:200px;"></td></tr>
    <tr  style="width:100%;background: #ecf0f5;"><th>注册人身份证正面照片</th><td>   <input style="width:250%;margin-bottom:10px;" type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[user_image_negative]" value=""><a href="<?php echo $model->user_image_negative ?>" target="_parent"><img  class="imgBtn" style="width:300px;height:200px;" src="<?php echo $model->user_image_negative ?>"/></a></td></tr>
    <tr style="width:100%;"><th>注册人身份证反面照片</th><td>   <input  style="width:250%;margin-bottom:10px;" type="text" id="companycategroy-name" class="form-control" name="CompanyCategroy[user_image_positive]" value=""><img class="imgBtn" style="width:300px;height:200px;" src="<?php echo $model->user_image_positive ?>"/></td></tr>
    
   
    <?php //ActiveForm::end(); ?>
    </tbody>
</table>
    <div class="form-group" style="width:100%;">
        <input style="margin:0 auto; display: block;background: #ff6600;border:none;border-radius: 3px;padding:3px 15px;color:#fff;" type="submit" value="确认审核"/>
    </div>
     </form>
</div>
