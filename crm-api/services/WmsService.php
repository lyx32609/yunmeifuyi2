<?php
namespace app\services;

use Yii;
use app\foundation\Service;
use app\models\UserSupplier;
use app\models\OrderDelivery;
use app\models\OrderBatch;
use app\models\OrderItem;
use app\models\User;



class WmsService extends Service
{
    /**
     * 创建订单
     * @param \app\models\Order $order
     */
    public function insertShippingOrder($order)
    {
        $ret = [];
    
        if($this->validate($order->supplier))
        {
            $ret = (array)\Yii::$app->wmsProxy->insertShippingOrder($order->supplier->wmsInfo, $order);
        }
        
        return $ret;
    }
    
    /**
     * 获取订单关联的GPS物理编号
     * @param \app\models\Order $order
     */
    public function getOrderGps($order)
    {
        $ret = false;

        if(!$order)
        {
            $this->setError('订单不存在');
            return false;
        }
        
        if($this->validate($order->supplier))
        {
            $ret = (array)\Yii::$app->wmsProxy->getOrderGps($order->supplier->wmsInfo, $order);
        }
        
        if($ret['ret'] === false)
        {
            $this->setError('获取定位信息失败:'.$ret['msg']);
            return false;
        }
        
        return $ret;
    }
    
    /* 
     * 获取车辆信息
     * @param $user_id 人员id
     *  */
    public function getCars($user_id)
    {
        if(empty($user_id))
        {
            $this->setError('参数不能为空');
            return false;
        }
        /* 查询该送货人员已经有存在的且未完成的送货批次 */
        $batch=OrderBatch::findOne(['user_id'=>$user_id,'status'=>1]);
        if($batch)
        {
            if($batch->batch_wms==='0'||empty($batch->batch_wms))
            {
                return [
                    'ret'=>2,
                    'result'=>[[
                        'car_id'=>$batch->car_id,
                        'car_name'=>$batch->car_name,
                        'car_driver_name'=>$batch->car_driver_name,
                        'car_driver_phone'=>$batch->car_driver_phone,
                    ]],
                ];
            }
            else
            {
                return [
                    'ret'=>'10',
                    'msg'=>'已经发车了哦！'
                ];
            }
        }
        
       
        $supplier_id=UserSupplier::findOne(['user_id'=>$user_id])->supplier_id;
        if(!$supplier_id)
        {
            $this->setError('获取供货商失败');
            return false;
        }
        $data=Yii::$app->api->request('basic/supplierVerify',['supplierId'=>$supplier_id]);
  
        if($data&&$data['ret']==0)
        {
            $ret=(array)\Yii::$app->wmsProxy->getCars($data['result'],$supplier_id);
        }
//         $supplier=Supplier::findOne(['uid'=>$supplier_id]);
//         if($this->validate($supplier))
//         {
//             $ret=(array)\Yii::$app->wmsProxy->getCars($supplier->wmsInfo,$supplier_id);
//         }
        else
        {
            $this->setError('供货商相关信息验证失败');
            return false;
        }
        
        /* 
         * $ret['ret']  true 成功  false 失败  30 username 获取失败  28 无空闲车辆
         *  */
        if($ret['ret'] === false)
        {
            $this->setError('获取车辆信息失败:'.$ret['msg']);
            return false;
        }
        if($ret['ret']===28)            
        {
            return [
                'ret'=>28,
                'result'=>$ret['msg']
            ];
        }
        if($ret['ret']===30)
        {
            return [
                'ret'=>30,
                'result'=>'供货商用户名获取失败'
            ];
        }
        /*
         * 去除已经选择的送货车辆
         *  */
        
        $user_ids = UserSupplier::findAll(['supplier_id'=>$supplier_id]);
        $use_car_array=[];
        foreach ($user_ids as $v)
        {
            $use_car_id=OrderBatch::findOne(['user_id'=>$v->user_id,'status'=>1])->car_id;
          
            if($use_car_id)
            {
                $use_car_array[]=$use_car_id;
            }
        }

  
        $result=[];
        if(!isset($ret['ListCapacity'])||empty($ret['ListCapacity']))
        {
            $this->setError('WMS车辆信息拉取失败');
            return false;
        }
        foreach ($ret['ListCapacity'] as $val)
        {
            if(empty($val['ID'])||empty($val['CAR_ID']))
            {
                $this->setError('返回车辆信息错误');
                return false;
            }
            if(!in_array($val['ID'], $use_car_array))
            {
                $arr=[
                    'car_id'=>$val['ID'],
                    'car_name'=>strtr($val['CAR_ID'],[' '=>'']),
                    'car_driver_name'=>$val['CarDriverName']?$val['CarDriverName']:'',
                    'car_driver_phone'=>$val['PHONE']?$val['PHONE']:''
                ];
                $result[]=$arr;
            }
        }
        return $result;
    }
    /* 
     * 发送送货单信息  ，给wms
     *  
     *  
     *  */
    public function deliveryNote($user_id)
    {
        if(empty($user_id))
        {
            $this->setError('参数不能为空');
            return false;
        }
        $supplier_id=UserSupplier::findOne(['user_id'=>$user_id])->supplier_id ;
        if(!$supplier_id)
        {
            $this->setError('获取供货商ID失败');
            return false;
        }
        
        $batch=OrderBatch::find()->andWhere(['user_id'=>$user_id,'status'=>1])->one();
        if(!$batch)
        {
            $this->setError('批次信息获取失败');
            return false;
        }
        $user=User::findOne(['id'=>$user_id]);
        if(!$user)
        {
            $this->setError('用户信息获取失败');
            return false;
        }
        $strTransportModel=[];
        $strTransportModel['ID']=$batch->car_id;
        $strTransportModel['CARID']=$batch->car_name;
        $strTransportModel['DRIVER_MAN']=$batch->car_driver_name;
        $strTransportModel['DRIVER_PHONE']=$batch->car_driver_phone;
        $strTransportModel['STATE ']='2';
        $strTransportModel['DOCUMENT_ID']=$user['username'];
        $strTransportModel['DOCUMENT_PERSON']=$user['name'];
        $deliveries=OrderDelivery::findAll(['batch_no'=>$batch->batch_no,'status'=>1,'batch_status'=>1]);
        if(!$deliveries)
        {
            $this->setError('发货信息查询失败,或已发车啦');
            return false;
        }
        else 
        {
            $strListTransportTab=[];
            foreach ($deliveries as $val)
            {
                $items=Yii::$app->api->request('basic/getOrderItem',['order_id'=>$val->order_id]);
 
              //  $items=OrderItem::findAll(['order_id'=>$val->order_id]);
                if($items&&$items['ret']==0)
                {
                    $items=$items['result'];
                }else{
                    $this->setError('订单详情获取失败');
                    return false;
                }
                foreach ($items as $item)
                {
                    $arr['ORDER_ID']=$val->order_id;
                    $arr['PRODUCT_ID']=$item['product_id'];
                    $arr['GOODS_ID']=$item['barcode'];
                    $arr['TR_NUM']=$item['nums'];
                }
                $strListTransportTab[]=$arr;
            }

        }
        $data=Yii::$app->api->request('basic/supplierVerify',['supplierId'=>$supplier_id]);
        if($data&&$data['ret']==0)
        {
            $ret=(array)\Yii::$app->wmsProxy->deliveryNote($data['result'],$strTransportModel,$strListTransportTab);
        }
        else
        {
            $this->setError('供货商相关信息验证失败');
            return false;
        }

        if($ret['ret'] === false)
        {
            $this->setError('打印送货单失败:'.$ret['msg']);
            return false;
        }
        else
        {
            
            $transaction = \Yii::$app->dbofficial->beginTransaction();  //开启事务
            try 
            {
                $batch->batch_wms=$ret['TRANSPORT_ID'];
                if(!$batch->save())
                {
                    throw new \Exception('回调发车编号失败');
                }
                foreach ($deliveries as $k=>$v){
                    $v->status=2;
                    $v->depart_time=$_SERVER['REQUEST_TIME'];

                    if(!$v->save())
                    {
                        throw new \Exception('订单发车失败');
                    }
                }
                $transaction->commit();
            }
            catch(\Exception $e)
            {
                $transaction->rollBack();
                $this->setError($e->getMessage());
                return false;
            } 
        }

        return '成功';
    }
    /* 
     * 通知 WMS 进行签收
     *  
     *  */
    
    public function paySign($supplier_id,$order_id)
    {
        if(!$order_id||!$supplier_id)
        {
            $this->setError('参数缺失');
            return false;
        }
        $data=Yii::$app->api->request('basic/supplierVerify',['supplierId'=>$supplier_id]);
        
        if($data&&$data['ret']==0)
        {
            $ret=(array)\Yii::$app->wmsProxy->paySign($data['result'],$order_id);
        }
     //   $supplier=Supplier::findOne(['uid'=>$supplier_id]);
//         if($this->validate($supplier))
//         {
//             $ret=(array)\Yii::$app->wmsProxy->paySign($supplier->wmsInfo,$order_id);
//         }
        else
        {
            $this->setError('供货商相关信息验证失败');
            return false;
        }
       
        if($ret['ret'] === false)
        {
            $this->setError('签收失败:'.$ret['msg']);
            return false;
        }
        return $ret;
            
    }
    private function validate($supplier)
    {
        return $supplier && $supplier->wms == 1 && $supplier->wmsInfo && $supplier->wmsInfo->api_addr;
    }
}