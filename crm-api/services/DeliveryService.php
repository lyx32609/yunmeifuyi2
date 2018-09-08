<?php
namespace app\services;

use app\foundation\Service;

use Yii;
use app\models\Supplier;  //发货商类
use app\models\UserSupplier;
use app\models\OrderBatch;
use app\models\OrderDelivery;
use app\models\SupplierProducts;
use app\models\MallProducts;
use app\models\SupplierGoods;
use app\models\MallGoods;
use app\models\Order;
use app\models\Member;
use app\models\OrderItem;
use app\models\OrderLog;
use app\models\User;
use app\benben\DateHelper;
use yii\data\Pagination;
use app\components\MathHelper;



class DeliveryService extends Service
{
    /* 
     * 查询仓库坐标
     *  
     *  */
    public function coordinate($user_id)
    {
        if(!empty($user_id))
        {
            $relevance=UserSupplier::findOne(['user_id'=>$user_id]);
          
            if($relevance)
            {
            //    $supplier=supplier::findOne(['uid'=>$relevance->supplier_id]);
                $supplier=Yii::$app->api->request('basic/getSupplier',['supplierId'=>$relevance->supplier_id]);
               
                if($supplier['ret']==0)
                {
                    $supplier=$supplier[0];
                    if(!empty($supplier['longitude'])&&$supplier['longitude']!=0&&!empty($supplier['latitude'])&&$supplier['latitude']!=0)
                    {
                        return [
                            'result'=>
                            [
                                'longitude'=>$supplier['longitude'],
                                'latitude'=>$supplier['latitude'],
                            ]
                        ];
                    }
                    else
                    {
                        $this->setError('缺少经纬度信息');
                        return false;
                    }                   
                }
                else
                {
                    $this->setError($supplier['msg']);
                    return false;     
                }
            }
            else
            {
                $this->setError('关联信息丢失');
                return false;   
            }
            
        }
        else
        {
            $this->setError('参数不能为空');
            return false; 
            
        }
    }
    
    /* 
     * 选定车辆 创建发货订单记录
     *    
     *  */
    public function batch($user_id,$car_id,$car_name,$car_driver_name,$car_driver_phone)
    {
        if(empty($user_id)||empty($car_id)||empty($car_name))
        {
            $this->setError('参数不能为空');
            return false;    
        }

        /* 查询该送货人员已经有存在的且未完成的送货批次 */
        $batch=OrderBatch::findOne(['user_id'=>$user_id,'status'=>1]);
        if($batch)
        {
            return [
                'ret'=>2,
                'result'=>[[
                    'car_id'=>$batch->car_id,
                    'car_name'=>$batch->car_name,
                    'car_driver_name'=>$batch->car_driver_name,
                    'car_driver_phone'=>$batch->car_driver_phone,
                ],
            ]];
        }
        $model=new OrderBatch();
        $model->user_id=$user_id;
        $model->car_id=$car_id;
        $model->car_name=$car_name;
        $model->car_driver_name=$car_driver_name;
        $model->car_driver_phone=$car_driver_phone;
        $model->batch_no=mt_rand(1000,9999).$_SERVER['REQUEST_TIME'];
        $model->batch_wms='0';
        $model->status=1; 
        $model->start_time=$_SERVER['REQUEST_TIME'];
        $model->end_time=0;
        if($model->save()){
            return  $result='成功';     
        }
        else
        {
            $this->setError('送货批次创建失败');
        }
    }
    
    
    /* 
     * 查询订单信息（及详细信息）
     *  
     *  */
    
    public function order($order_id)
    {
        if(!empty($order_id))
        {
            $order=Yii::$app->api->request('basic/getOrder',['order_id'=>$order_id]);
            if($order['ret']===0)
            {
                $order=$order[0];
            }else{
                $this->setError('订单信息获取失败');
                return false;
            }
           // $order=Order::findOne(['order_id'=>$order_id,'status'=>'finish']);
            //订单
            if($order)
            {
                $goods=Yii::$app->api->request('basic/getOrderItem',['order_id'=>$order_id]);
                if($goods['ret']===0)
                {
                    $goods=$goods['result'];
                }else{
                    $this->setError('订单详情信息获取失败');
                    return false;
                }
               // $order_orders=OrderItem::findAll(['order_id'=>$order_id]);
                //订单详情
                if($goods)
                {
                    $member=Yii::$app->api->request('basic/getMember',['member_id'=>$order['member_id']]);
                    //商户信息
                    if($member&&$member['ret']==0)
                    {
                        $member=$member[0];
                        $order_delivery=OrderDelivery::findOne(['order_id'=>$order_id]);
                        //待收货款
                        $money=MathHelper::sub(array($order['total_amount'], $order['payed'],$order['pmt_amount_platform'], $order['use_coins']/100));
                        $money=$money==0?0:$money;
                        if($order['ship_status']==5&&$order_delivery->pay_sign_status==2)
                        {
                            $money='';
                        }
                        $result=[
                            'order_id'=>$order['order_id'],
                            'shopname'=>$member['shopname'],
                            'longitude'=>$member['longitude'],
                            'latitude'=>$member['latitude'],
                            'tel'=>$order['ship_mobile'],
                            'address'=>$order['ship_addr'],
                            'remarks'=>$order['memo'],
                            //总账款
                            'total_amount'=>$order['final_amount'],   

                            'receivable'=>$money,
                            'goods'=>$goods,
                        ];                            
                        return $result;      
                    }
                    else 
                    {
                        $this->setError('商户信息获取失败');
                        return false;
                    }
                }
                else 
                {
                    $this->setError('订单明细不存在');
                    return false;
                }
            }
            else                
            {
                $this->setError('订单不存在');
                return false;
            }
        }
        else 
        {
            $this->setError('参数不能为空');
            return false;
        }
    }
    /* 
     * 确认发货（创建订单发货详情记录表)
     *  
     *  */
    public function delivery_record($order_id,$user_id)
    {
        if(!empty($order_id)&&!empty($user_id))
        {
            /* 判断订单 */
        //    $order=Order::findOne(['order_id'=>$order_id,'ship_status'=>['0','1']]);
            $order=Yii::$app->api->request('basic/getOrder',['order_id'=>$order_id]);
            if($order['ret']==0)
            {
                $order=$order[0];
            }else{
                $this->setError('订单信息获取失败或者订单已完成配送');
                return false;
            }
            if(!in_array($order['ship_status'], ['0','1']))
            {
                $this->setError('订单状态非正常');
                return false;
            }

            /* 判断订单记录 */
            $delivery=OrderDelivery::findOne(['order_id'=>$order_id,'batch_status'=>1]);
            if($delivery)
            {
                $this->setError('该订单发货中...');
                return false;
            }
            /* 判断批次 */
            $batch=OrderBatch::find()->andWhere(['user_id'=>$user_id,'status'=>1])->one();
            if(!$batch||$batch->batch_wms!='0')
            {
                $this->setError('批次信息获取失败或者已经发车');
                return false;
            }
            
            $model=new OrderDelivery();
            $model->user_id=$user_id;
            $model->order_id=$order_id;
            $model->member_id=$order['member_id'];
            $model->car_id=$batch->car_id;
            $model->status=1;
            $model->scan_time=$_SERVER['REQUEST_TIME'];
            $model->depart_time=0;
            $model->sign_for_time=0;
            $model->batch_no=$batch->batch_no;
            $model->batch_status=1;
            $pay=MathHelper::sub(array($order['total_amount'], $order['payed'],$order['pmt_amount_platform'], $order['use_coins']/100));
            $model->pay_sign_status=$pay==0?0:1;
            
            if($model->save())
            {
                /* 做发货记录 */
                $log_text = '订单发货成功';
                $behavior = '发货';
                $result = 'success';
                $log='记录发货出错';
                $this->addLog($order_id, $log_text, $behavior, $result, $log);
                
                return true;
            }
            else
            {
                $this->setError('订单发货详情表创建失败');
                return false;
            }
            
        }
        else
        {
            $this->setError('参数不能为空');
            return false;
        }
    }
    /*
     * 签收
     * @param int $order_id 订单号
     * @param string $payMent 待收货款
	 * @param int $is_paySign 是否确认收款  1：未确认 2：确认
	 * 
     *  */
    public function updatePaysign($order_id,$payMent,$is_paySign,$user_id)
    {
        /* 判断批次 */
        $batch=OrderBatch::find()->andWhere(['user_id'=>$user_id,'status'=>1])->one();
        if(!$batch)
        {
            $this->setError('批次信息获取失败或者已经发车');
            return false;
        }
        $orderDelivery = OrderDelivery::find()
        ->andWhere('order_id = :order_id',array(':order_id'=>$order_id))
        ->andWhere('depart_time !=0')
        ->andWhere("status != 3")
        ->andWhere('batch_no=:batch_no',[':batch_no'=>$batch->batch_no])
        ->one();
        $order=Yii::$app->api->request('basic/getOrder',['order_id'=>$order_id]);
            if($order['ret']==0)
            {
                $order=$order[0];
            }else{
                $this->setError('订单信息获取失败或者订单已完成配送');
                return false;
            }
        if(!$orderDelivery || !$order)
        {
            $this->setError('订单不存在或已签收');
            return false;
        }
        $wms_result=\app\services\WmsService::instance()->paySign($order['company_id'],$order_id);
        
        if($wms_result['ret']==false)
        {

            $this->setError('签收失败:'.$wms_result['msg']); 
            return false;
        }    

        $orderDelivery->status = 3;
        $orderDelivery->sign_for_time = time();
        $orderDelivery->pay_sign_status = $is_paySign;
     
        $arr=[
            'ship_status'=>'5','getted_time'=>time(),
            
        ];
        $data=Yii::$app->api->request('alter/alterOrderStatus',['order_id'=>$order_id,'params'=>json_encode($arr)]);
        if($data&&$data['ret']===0)
        {
            
        }else{
            $this->setError($data['msg']);
            return false;
        }
        if(!$orderDelivery->save())
        {
            $this->setError('签收失败', $orderDelivery->errors);
            return false;
        }
       
        //添加签收日志
        $log_text = '订单签收成功,收到货款'.$payMent;
        $behavior = '签收';
        $result = 'success';
        $log = '添加签收日志失败';
        $this->addLog($order_id, $log_text, $behavior, $result, $log);
        return true;
    }
    /*
     * 发货列表
     *
     *  */
    public function deliverList($page=1,$pageCount)
    {
        $uid = \Yii::$app->user->id;
        $query = OrderDelivery::find()
        ->select('order_id,depart_time,pay_sign_status,member_id')
        ->andWhere('user_id = :uid',[':uid'=>$uid])
        ->andWhere('batch_status = 1')
        ->andWhere('status != 3');
        $pagination = new Pagination([
            'params'=>['page'=>$page],
            'defaultPageSize' => $pageCount?$pageCount:$query->count(),
            'totalCount' => $query->count(),
        ]);
         
        $rows = $query->offset($pagination->offset)
        ->limit($pagination->limit)
        ->asArray()->orderBy('sign_for_time asc')->all();
        $array=[];
        foreach ($rows as $row)
        {
            $member=Yii::$app->api->request('basic/getMember',['member_id'=>$row['member_id']]);
            if($member&&$member['ret']===0)
            {  
                $row['shopname']=$member[0]['shopname'];
                $row['latitude']=$member[0]['latitude'];
                $row['longitude']=$member[0]['logitude'];
                $array[]=$row;
            }else{
                $this->setError('供货商获取失败');
                return false;
            }
        }
        
        return ['result'=>$array, 'pageCount'=>$pagination->pageCount];
    }
    /*
     * 签收列表
     *@param int $type 订单状态(1已签收、2未签收)
     *@param  string   $start_time 开始时间
     *@param  string   $end_time 结束时间
     *@param  int   $page 页数 不传默认1
     *@param  int   $pageCount 每页个数 不传默认全部
     *
     *  */
    public function deliverSignList($type,$start_time,$end_time,$page=1,$pageCount)
    {
        $start = strtotime($start_time);
        $end = strtotime($end_time);
        $start = $start ? $start : DateHelper::getDayStartTime(7);
        $end = $end ? $end : strtotime(date('Y-m-d',time()));
        $end += 86400;
        $uid = \Yii::$app->user->id;
        if ($type==1)
        {
           $query = OrderDelivery::find()
                  ->select('order_id,sign_for_time,pay_sign_status,member_id')
                  ->andWhere('user_id = :uid',[':uid'=>$uid])
                  ->andWhere('sign_for_time >= :start and sign_for_time < :end', [':start'=>$start, ':end'=>$end])
                  ->andWhere('status = 3');
        }
        else
        {
            $query = OrderDelivery::find()
                    ->select('order_id,sign_for_time,pay_sign_status,member_id')
                    ->andWhere('user_id = :uid',[':uid'=>$uid])
                    ->andWhere('scan_time >= :start and scan_time < :end', [':start'=>$start, ':end'=>$end])
                    ->andWhere('status != 3')
                    ->andWhere('batch_status = 2');
        } 
        $pagination = new Pagination([
            'params'=>['page'=>$page],
            'defaultPageSize' => $pageCount?$pageCount:$query->count(),
            'totalCount' => $query->count(),
        ]);
         
        $rows = $query->offset($pagination->offset)
        ->limit($pagination->limit)
        ->asArray()->orderBy('sign_for_time asc')->all();
        $array=[];
        foreach ($rows as $row)
        {
            $member=Yii::$app->api->request('basic/getMember',['member_id'=>$row['member_id']]);
            if($member&&$member['ret']===0)
            {
                $row['shopname']=$member[0]['shopname'];
                $row['latitude']=$member[0]['latitude'];
                $row['longitude']=$member[0]['logitude'];
                $array[]=$row;
            }else{
                $this->setError('供货商获取失败');
                return false;
            }
        }
        return ['result'=>$array, 'pageCount'=>$pagination->pageCount];
    }
   
    
    /* 
     * 送货完成（该批次送货完成，收车结束）
     *  
     *  
     *  */
    public function finish($user_id,$flag)
    {
        if(empty($user_id))
        {
            $this->setError('参数不能为空');
            return false;
        }
        $batch=OrderBatch::findOne(['user_id'=>$user_id,'status'=>1]);
        if(!$batch)
        {
            $this->setError('获取批次信息失败');
            return false;
        }
        if($flag==1)
        {
            $batch->status=0;
        }
        else
        {
            $batch->status=2;
        }
        
        $batch->end_time=$_SERVER['REQUEST_TIME'];
        if($batch->save())
        {
            $deliveries=OrderDelivery::findAll(['batch_no'=>$batch->batch_no,'batch_status'=>1]);
            if(!$deliveries)
            {
                return [
                    'ret'=>10,
                    'msg'=>'收车完成，但批次订单未找到',
                ];
            }
            $transaction = \Yii::$app->dbofficial->beginTransaction();  //开启事务
            try 
            {
                foreach ($deliveries as $v){
                    if($flag==1)
                    {
                        $v->batch_status=0;                        
                    }
                    else 
                    {
                        $v->batch_status=2;                        
                    }
                    
                    if(!$v->save())
                    {
                        throw new \Exception('批次订单操作失败');
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
        else 
        {
            $this->setError('当前批次状态操作失败');
            return false;
        }
        return '收车成功，批次订单成功';
    }
    /* 
     * 获取该批次下所有订单商铺的坐标
     *  
     *  */
    public function coordinates($user_id)
    {
        if(empty($user_id))
        {
            $this->setError('参数不能为空');
            return false;
        }
        $batch=OrderBatch::findOne(['user_id'=>$user_id,'status'=>1]);
        if(!$batch)
        {
            $this->setError('获取批次信息失败');
            return false;
        }

        $supplier_id=UserSupplier::findOne(['user_id'=>$user_id])->supplier_id;
        
        $supplier=Yii::$app->api->request('basic/getSupplier',['supplierId'=>$supplier_id]);
        if($supplier&&$supplier['ret']==0)
        {
            $supplier=$supplier[0];
        }else{
            $this->setError('获取供货商信息失败');
            return false;
        }
        $coordinates=[];
        $coordinates['supplier_coordinate']=[
            'company_name'=>$supplier['company_name'],
            'longitude'=>$supplier['longitude'],
            'latitude'=>$supplier['latitude'],
        ];
        $deliveries=OrderDelivery::find()->andWhere(['batch_no'=>$batch->batch_no])->groupBy('member_id')->all();
        if(!$deliveries)
        {
            $this->setError('获取订单发货信息失败');
            return false;
        }
        $array=[];
        $id_arr=[];
        foreach ($deliveries as $val)
        {
            $member=Yii::$app->api->request('basic/getMember',['member_id'=>$val->member_id]);
            if($member&&$member['ret']===0)
            {
                $member=$member[0];
            }else{
                $this->setError('获取采购商信息失败 ！');
                return false;
            }

            $array[]=[
                'member_id'=>$member['member_id'],
                'shopname'=>$member['shopname'],
                'mobile'=>$member['mobile'],
                'addr'=>$member['addr'],
                'longitude'=>$member['longitude'],
                'latitude'=>$member['latitude'],
            ];           
        }
        $coordinates['member_coordinate']=$array;
        return $coordinates;
    }
    /*
     * 添加相关操作日志
     *
     *  */
    private function addLog($order_id,$log_text,$behavior,$result,$log)
    {
        $orderLog = new OrderLog();
        $uid = \Yii::$app->user->id;
        $user = User::findOne($uid);
        $orderLog->order_id = $order_id;
        $orderLog->op_id = $user['username'];
        $orderLog->op_name = $user['name'];
        $orderLog->log_text = $log_text;
        $orderLog->acttime = time();
        $orderLog->behavior = $behavior;
        $orderLog->result = $result;
        if(!$orderLog->save())
        {
            $this->setError($log, $orderLog->errors);
            return false;
        }
    
        return true;
    }
  
}