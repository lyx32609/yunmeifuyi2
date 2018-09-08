<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\models\Regions;
use app\models\CompanyGoods;
use app\models\CompanyService;
use app\models\CompanyProduct;
use backend\models\CompanyCategroy;

/* @var $this yii\web\View */
/* @var $model backend\models\CompanyCategroy */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => '查看子公司', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="company-categroy-view">

    <h1><?= Html::encode($this->title) ?></h1>

<!--     <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p> -->
    <?php 
                if($model->status == 0){ $status = "运营商";}
                if($model->status == 1){ $status = "销售商";}
                if($model->status == 2){ $status = "供货商";}
                if($model->status == 3){ $status = "配送商";}
                if($model->status == 4){ $status = "生产商";}
                if($model->status == 5){ $status = "服务商";}
                
                $createtime = date('Y-m-d',$model->createtime);

                $area_data = Regions::find($model->area_id)->select(['local_name'])->where(["region_id"=>$model->area_id])->one();

                $city_data = Regions::find($model->domain_id)->select(['local_name'])->where(["region_id"=>$model->domain_id])->one();

                if($model->fly == 0){$fly = "主公司";}
                if($model->fly != 0){$fly_data = CompanyCategroy::find()->where(["id"=>$model->fly])->one();$fly = $fly_data['name'];}

                if($model->type == 0){$type = '未合作';}
                if($model->type == 1){$type = '已合作';}

                if($model->review ==0){$review = "待审核";}
                if($model->review ==1){$review = "审核中";}
                if($model->review ==2){$review = "审核通过";}
                if($model->review ==3){$review = "审核未通过";}

                if($model->failure == 0){$failure = "永久使用";}
                if($model->failure == 1){$failure = "试用";}

                $goods_data = CompanyGoods::find()->select(['goods_name'])->where(["id"=>$model->goods_type])->one();

                $service_data = CompanyService::find()->select(['service_name'])->where(["id"=>$model->service_type])->one();

                $product_data = CompanyProduct::find()->select(['product_name'])->where(["id"=>$model->product_type])->one();
                
                
?>

<?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
            'label'=>'企业名称',
            'format'=>'raw',
            'value' => $model->name
            ],
            
            
            [
                'label'=>'企业类型',
                'format'=>'raw',
                'value' =>$status
            ],
            
            
            
            [
            'label'=>'创建日期',
            'format' => 'raw',
            'value' => $createtime
            ],
            
            
            [
            'label'=>'联系方式',
            'format'=>'raw',
            'value' => $model->phone
             ],
            [
            'label'=>'省',
            'format'=>'raw',
            'value' => $area_data['local_name']
            ],
                        [
            'label'=>'地区',
            'format'=>'raw',
            'value' => $city_data['local_name']
            ],
                        [
            'label'=>'主公司',
            'format'=>'raw',
            'value' => $fly
            ],
                        [
            'label'=>'合作方式',
            'format'=>'raw',
            'value' => $type
            ],
                        [
            'label'=>'审核状态',
            'format'=>'raw',
            'value' => $review
            ],
                        [
            'label'=>'执照编号',
            'format'=>'raw',
            'value' => $model->license_num
            ],
                        [
            'label'=>'注册资金',
            'format'=>'raw',
            'value' => $model->register_money
            ],
                        [
            'label'=>'经营面积',
            'format'=>'raw',
            'value' => $model->business
            ],
                                    [
            'label'=>'经营地址',
            'format'=>'raw',
            'value' => $model->business_ress
            ],
                                    [
            'label'=>'人员数量',
            'format'=>'raw',
            'value' => $model->staff_num
            ],
                                    [
            'label'=>'代理品牌',
            'format'=>'raw',
            'value' => $model->acting
            ],
                                                [
            'label'=>'代理级别',
            'format'=>'raw',
            'value' => $model->proxy_level
            ],
                                                [
            'label'=>'服务区域',
            'format'=>'raw',
            'value' => $model->service_area
            ],
                                                [
            'label'=>'配送商户',
            'format'=>'raw',
            'value' => $model->distribution_merchant
            ],
                                                            [
            'label'=>'配送车辆',
            'format'=>'raw',
            'value' => $model->distribution_car
            ],
                                                            [
            'label'=>'配送人员',
            'format'=>'raw',
            'value' => $model->distribution_staff
            ],
                                                            [
            'label'=>'商品数量',
            'format'=>'raw',
            'value' => $model->goods_num
            ],
                                                            [
            'label'=>'配送商户',
            'format'=>'raw',
            'value' => $model->distribution_merchant
            ],
                                                            [
            'label'=>'配送车辆',
            'format'=>'raw',
            'value' => $model->distribution_car
            ],
                                                            [
            'label'=>'配送人员',
            'format'=>'raw',
            'value' => $model->distribution_staff
            ],
                                                            [
            'label'=>'商品数量',
            'format'=>'raw',
            'value' => $model->goods_num
            ],
                                                            [
            'label'=>'使用状态',
            'format'=>'raw',
            'value' => $failure
            ],
                                                            [
            'label'=>'商品类型',
            'format'=>'raw',
            'value' => $goods_data['goods_name']
            ],
                                                            [
            'label'=>'服务类型',
            'format'=>'raw',
            'value' => $service_data['service_name']
            ],
              [
            'label'=>'产品类型',
            'format'=>'raw',
            'value' => $product_data['product_name']
            ],
            [
            'label'=>'服务区域',
            'format'=>'raw',
            'value' => $model->salas_business
            ],
                                                            [
            'label'=>'营业执照',
            'format'=>'image',
            'image' => [
                'width'=>200,
                'height'=>200
            ],
            'value' => $model->license_image
            ],
                                                                        [
            'label'=>'身份证正面照',
            'format'=>'image',
            'image' => [
                'width'=>200,
                'height'=>200
            ],
            'value' => $model->user_image_negative
            ],
                                                                        [
            'label'=>'身份证反面照',
            'format'=>'image',
            'image' => [
                'width'=>200,
                'height'=>200
            ],
            'value' => $model->user_image_positive
            ],
        ],
    ]) ?>

</div>
