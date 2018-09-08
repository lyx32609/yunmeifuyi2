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
use app\models\CompanyCategroy;
use app\models\Shop;
use app\benben\DateHelper;
use yii\data\Pagination;
use app\components\MathHelper;
use app\models\CompanyInterface;
use app\models\BindCar;
use app\models\CompanyOrder;
use app\services\HttpCurlService;
use app\benben\NetworkHelper;

 
class DeliveryNewService extends Service
{
    /* 
     * 获取该批次下所有订单商铺的坐标
     *  
     *  */
    public function getAllPosition($user_id, $is_cooperation, $company_category_id)
    {
        if(empty($user_id) || empty($company_category_id))
        {
            $this->setError('参数不能为空');
            return false;
        }
        if($is_cooperation == 0){
            $company_interface = CompanyInterface::findOne(['company_id' => $company_category_id, 'module_id' => 13]);
            if(!$company_interface){
                $this->setError('请先添加接口');
                return false;
            }
            $bindCars = BindCar::findOne(['user_id' => $user_id]);
            if(!$bindCars){
                $this->setError('无绑定车辆');
                return false;
            }
            $param = [
                'car_id' => $bindCars->car_id
            ];
            $http = HttpCurlService::instance();
            $result = $http->request($company_interface->url, $company_interface->public_key . $company_interface->privace_key, $param, $company_interface->protocol);
            if(!$result){
                $this->setError('车辆未送货或获取店铺列表失败');
                return false;
            }
            return $result;
        }
        $batch = OrderBatch::findOne(['user_id' => $user_id, 'status' => 1]);
        if(!$batch)
        {
            $this->setError('获取批次信息失败');
            return false;
        }
        $user_data = User::find()
                   ->select(["company_categroy_id",'id','username'])
                   ->where(["id" => $user_id])
                   ->one();
        $company_id = $user_data['company_categroy_id'];
        $company_data = CompanyCategroy::find()
                      ->select(["name", "fly"])
                      ->where(["id"  => $company_id])
                      ->asArray()
                      ->one();
        if(!$company_data)
        {
            $this->setError("该企业不存在");
            return false;
        }
        if($is_cooperation == 1)//云媒及其子公司
        {
            $result = $this->coordinates($user_id);
        }
        else
        {
            $result = $this->getLocalPosition($user_id);
        }
        return $result;
    }


    /*云媒调取店铺信息*/
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
    
   /*获取批次店铺信息（本地）*/
    public function getLocalPosition($user_id)
    {
        $supplier_data = UserSupplier::find()
                    ->select(["supplier_id"])
                    ->where(["user_id" => $user_id])
                    ->asArray()
                    ->one();
        $supplier_id = $supplier_data['supplier_id'];
        if($supplier_id)
        {
                $supplier = Shop::find()
                ->select(["off_shop.shop_longitude","off_shop.shop_latitude","name"])
                //->where(["off_shop.id" => $supplier_id])
                ->andWhere(["shop_type" =>2])
                ->leftJoin(["off_company_categroy","off_shop.company_category_id = off_company_categroy.id"])
                ->asArray()
                ->one(); 
        }
        else
        {
                $this->setError('获取供货商信息失败');
                return false;
        }
        if($supplier)
        {
            $supplier = $supplier;
        }else
        {
            $this->setError('获取供货商信息失败');
            return false;
        }
        $coordinates=[];
        $coordinates['supplier_coordinate']=[
            'company_name'=>$supplier['name'],
            'longitude'=>$supplier['shop_longitude'],
            'latitude'=>$supplier['shop_latitude'],
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
            //$member=Yii::$app->api->request('basic/getMember',['member_id'=>$val->member_id]);
            $member = Shop::find()
                    ->select(["id","shop_name","phone","shop_addr","shop_longitude","shop_latitude"])
                    ->where(["id" => $val['member_id']])
                    ->andWhere(["shop_type" => 3])
                    ->asArray()
                    ->one(); 
            if($member)
            {
                $member = $member;
            }else
            {
                    $this->setError('获取采购商信息失败 ！');
                    return false;
            }
            $array[]=[
                        'member_id'=>$member['id'],
                        'shopname'=>$member['shop_name'],
                        'mobile'=>$member['phone'],
                        'addr'=>$member['shop_addr'],
                        'longitude'=>$member['shop_longitude'],
                        'latitude'=>$member['shop_latitude'],
                    ];           
        }
        $coordinates['member_coordinate'] = $array;
        return $coordinates;
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
    public function getSignList($company_id, $type, $start_time, $end_time, $page, $pageCount, $is_cooperation)
    {
        if(!$company_id)
        {
            $this->setError("企业ID不能为空");
            return false;
        }
        if(!$type)
        {
            $this->setError("订单状态不能为空");
            return false;
        }
       
        
        if($is_cooperation != 0)//云媒及其子公司
        {
            $service = DeliveryService::instance();
            $result = $service->deliverSignList($type, $start_time, $end_time, $page, $pageCount);
        }
        else
        {
            $result = $this->getLocalSignList($type, $start_time, $end_time, $page, $pageCount);
        }
        return $result;
    } 

    /*获取本地签收记录*/
    public function getLocalSignList($type, $start_time, $end_time, $page, $pageCount)
    {
        $start = strtotime($start_time);
        $end = strtotime($end_time);
        $start = $start ? $start : DateHelper::getDayStartTime(7);
        $end = $end ? $end : strtotime(date('Y-m-d',time()));
        $end += 86400;
        $uid = \Yii::$app->user->id;
        if ($type==1) 
        {
           $query = CompanyOrder::find()
                    ->select('order_id, end_time, order_pay, member_name, status')
                    ->andWhere('user_id = :uid', [':uid'=>$uid])
                    ->andWhere('status = 2');
            $rows = $query->offset($pagination->offset)
                ->limit($pagination->limit)
                ->asArray()->orderBy('end_time asc')
                ->all();        
        }
        else
        {
            $query = CompanyOrder::find()
                    ->select('order_id, create_time, order_pay, member_name, status')
                    ->andWhere('user_id = :uid', [':uid'=>$uid])
                    ->andWhere('status = 1');
            $rows = $query->offset($pagination->offset)
                ->limit($pagination->limit)
                ->asArray()->orderBy('create_time asc')
                ->all(); 
        } 
        $pagination = new Pagination([
            'params'=>['page'=>$page],
            'defaultPageSize' => $pageCount?$pageCount:$query->count(),
            'totalCount' => $query->count(),
        ]);
         
        
        $array = [];
        if(!$rows){
            $this->setError('暂无数据');
            return false;
        }
        for($i = 0; $i < count($rows); $i++){
            $array[$i]['sign_for_time'] = $rows[$i]['create_time'] ? $rows[$i]['create_time'] : $rows[$i]['end_time'];
            $array[$i]['order_id'] = $rows[$i]['order_id'];
            $array[$i]['pay_sign_status'] = $rows[$i]['status'];
            $array[$i]['shopname'] = $rows[$i]['member_name'];
        }
        return ['result' => $array, 'pageCount' => $pagination->pageCount];
    }

    /*发货列表*/
    public function getList($company_id, $page, $pageCount, $is_cooperation, $user_id)
    {
        if(!$company_id || !$user_id)
        {
            $this->setError("参数不能为空");
            return false;
        }
           
        if($is_cooperation != 0)
        {
            $service = DeliveryService::instance();
            $result = $service->deliverList($page,$pageCount);
        } else {
            $result = $this->getLocalList($page, $pageCount);
        }
        return $result;
            
    }

    public function getLocalList($page,$pageCount)
    {
        $uid = \Yii::$app->user->id;
        $query = CompanyOrder::find()
                ->select('order_id, create_time, order_pay, member_name, status')
                ->andWhere('user_id = :uid', [':uid'=>$uid])
                ->andWhere('status = 1');
        $pagination = new Pagination([
            'params'=>['page'=>$page],
            'defaultPageSize' => $pageCount?$pageCount:$query->count(),
            'totalCount' => $query->count(),
        ]);
         
        $rows = $query->offset($pagination->offset)
                ->limit($pagination->limit)
                ->asArray()->orderBy('create_time asc')
                ->all();
        $array = [];
        if(!$rows){
            $this->setError('暂无数据');
            return false;
        }
        for($i = 0; $i < count($rows); $i++){
            $array[$i]['depart_time'] = $rows[$i]['create_time'];
            $array[$i]['order_id'] = $rows[$i]['order_id'];
            $array[$i]['pay_sign_status'] = $rows[$i]['status'];
            $array[$i]['shopname'] = $rows[$i]['member_name'];
        }
        return ['result' => $array, 'pageCount' => $pagination->pageCount];
    }
     /* 
     * 选定车辆 创建发货订单记录(改版后)
     *    
     *  */
    public function batch($user_id, $car_id, $car_name, $car_driver_name, $car_driver_phone, $is_cooperation, $company_category_id)
    {
        if(empty($user_id) || empty($car_id) || empty($car_name))
        {
            $this->setError('参数不能为空');
            return false;    
        }
        if($is_cooperation == 0){
            $user = User::findOne($user_id);
            $bindCars = BindCar::findOne(['user_id' => $user_id]);
            if(!$bindCars){
                $bind = new BindCar();
                $bind->user_id = $user_id;
                $bind->car_name = $car_name;
                $bind->car_id = $car_id;
                $bind->user_name = $user->name;
                $bind->user_phone = $user->phone;
                $bind->status = 1;
                if(!$bind->save()){
                    $this->setError('车辆绑定失败');
                    return false;
                }
            }
            return $result = '绑定成功';
        } 
            /* 查询该送货人员已经有存在的且未完成的送货批次 */
            $batch = OrderBatch::findOne(['user_id' => $user_id, 'status' => 1]);
        
        if($batch)
        {
            return [
                'ret' => 2,
                'result' => [[
                    'car_id' => $batch->car_id,
                    'car_name' => $batch->car_name,
                    'car_driver_name' => $batch->car_driver_name,
                    'car_driver_phone' => $batch->car_driver_phone,
                ],
            ]];
        }
        $model = new OrderBatch();
        $model->user_id = $user_id;
        $model->car_id = $car_id;
        $model->car_name = $car_name;
        $model->car_driver_name = $car_driver_name;
        $model->car_driver_phone = $car_driver_phone;
        $model->batch_no = mt_rand(1000,9999).$_SERVER['REQUEST_TIME'];
        $model->batch_wms = '0';
        $model->status = 1; 
        $model->start_time = $_SERVER['REQUEST_TIME'];
        $model->end_time = 0;
        if($model->save()){
            return  $result = '成功';     
        }
        else
        {
            $this->setError('送货批次创建失败');
        }
    }
    /* 
     * 送货完成（该批次送货完成，收车结束）(改版后)
     *  
     *  
     *  */
    public function finishNew($user_id, $flag, $is_cooperation, $company_category_id, $car_id, $car_name)
    {
        if(empty($user_id)  || empty($company_category_id))
        {
            $this->setError('参数不能为空');
            return false;
        }
        if($is_cooperation == 0) {
            if($flag){
                $company_interface = CompanyInterface::findOne(['company_id' => $company_category_id, 'module_id' => 5]);
            } else {
                $company_interface = CompanyInterface::findOne(['company_id' => $company_category_id, 'module_id' => 10]);
            }
            if(!$company_interface){
                $this->setError('请先添加接口');
                return false;
            }
            $param = [
                'car_id' => $car_id,
                'car_name' => $car_name
            ];
            $http = HttpCurlService::instance();
            $result = $http->request($company_interface->url, $company_interface->public_key . $company_interface->privace_key, $param, $company_interface->protocol);
            if($result){
                $bindCars = BindCar::findOne(['user_id' => $user_id]);
                if($bindCars){
                    $bindCars->delete();
                }
                
                return $result = '收车成功';
            } 
            return $result = '收车失败';
        }
        $batch = OrderBatch::findOne(['user_id' => $user_id, 'status' => 1]);
        if(!$batch)
        {
            $this->setError('获取批次信息失败');
            return false;
        }
        if($flag == 1)
        {
            $batch->status = 0;
        }
        else
        {
            $batch->status = 2;
        }
        
        $batch->end_time = $_SERVER['REQUEST_TIME'];
        if($batch->save())
        {
            $deliveries=OrderDelivery::findAll(['batch_no' => $batch->batch_no, 'batch_status' => 1]);
            if(!$deliveries)
            {
                return [
                    'ret' => 10,
                    'msg' => '收车完成，但批次订单未找到',
                ];
            }
            $transaction = \Yii::$app->dbofficial->beginTransaction();  //开启事务
            try 
            {
                foreach ($deliveries as $v){
                    if($flag == 1)
                    {
                        $v->batch_status = 0;                        
                    }
                    else 
                    {
                        $v->batch_status = 2;                        
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
     * 查询订单信息（及详细信息）(改版后)
     *  
     *  */
    
    public function orderNew($order_id, $is_cooperation, $company_category_id)
    {
        if(!$company_category_id){
            $this->setError('参数不能为空');
            return false;
        }
        if($is_cooperation == 0){
            $company_interface = CompanyInterface::findOne(['company_id' => $company_category_id, 'module_id' => 6]);
            if(!$company_interface){
                $this->setError('请先添加接口');
                return false;
            }
            $param = [
                'order_id' => $order_id,
            ];
            $http = HttpCurlService::instance();
            $result = $http->request($company_interface->url, $company_interface->public_key . $company_interface->privace_key, $param, $company_interface->protocol);
            // $result = NetworkHelper::makeRequest($company_interface->url, $param,'','post', $company_interface->protocol);
            if(!$result){
                $this->setError('订单信息获取失败');
                return false;
            }
            return $result;
        }
        if(!empty($order_id))
        {
            $order = Yii::$app->api->request('basic/getOrder',['order_id'=>$order_id]);
            if($order['ret'] === 0)
            {
                $order = $order[0];
            }else{
                $this->setError('订单信息获取失败');
                return false;
            }
           // $order=Order::findOne(['order_id'=>$order_id,'status'=>'finish']);
            //订单
            if($order)
            {
                $goods = Yii::$app->api->request('basic/getOrderItem', ['order_id' => $order_id]);
                if($goods['ret'] === 0)
                {
                    $goods = $goods['result'];
                }else{
                    $this->setError('订单详情信息获取失败');
                    return false;
                }
               // $order_orders=OrderItem::findAll(['order_id'=>$order_id]);
                //订单详情
                if($goods)
                {
                    $member = Yii::$app->api->request('basic/getMember',['member_id' => $order['member_id']]);
                    //商户信息
                    if($member && $member['ret'] == 0)
                    {
                        $member = $member[0];
                        $order_delivery = OrderDelivery::findOne(['order_id' => $order_id]);
                        //待收货款
                        $money = MathHelper::sub(array($order['total_amount'], $order['payed'], $order['pmt_amount_platform'], $order['use_coins'] / 100));
                        $money = $money == 0 ? 0 : $money;
                        if($order['ship_status'] == 5 && $order_delivery->pay_sign_status == 2)
                        {
                            $money='';
                        }
                        $result=[
                            'order_id' => $order['order_id'],
                            'shopname' => $member['shopname'],
                            'longitude' => $member['longitude'],
                            'latitude' => $member['latitude'],
                            'tel' => $order['ship_mobile'],
                            'address' => $order['ship_addr'],
                            'remarks' => $order['memo'],
                            //总账款
                            'total_amount' => $order['final_amount'],   

                            'receivable' => $money,
                            'goods' => $goods,
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
     * 确认发货（创建订单发货详情记录表)(改版后)
     *  
     *  */
    public function delivery_recordNew($order_id, $user_id, $is_cooperation, $company_category_id, $car_id, $car_name, $order_money, $order_pay, $member_name)
    {
        if(!empty($order_id) && !empty($user_id) &&  !empty($company_category_id)&& !empty($car_id)&& !empty($car_name))
        {
            if($is_cooperation == 0){
                $company_interface = CompanyInterface::findOne(['company_id' => $company_category_id, 'module_id' => 11]);
                if(!$company_interface){
                    $this->setError('请先添加接口');
                    return false;
                }
                $user = User::findOne($user_id);
                $param = [
                    'order_is' => $order_id,
                    'username' => $user->name,
                    'car_id' => $car_id,
                    'car_name' => $car_name
                ];
                $http = HttpCurlService::instance();
                $result = $http->request($company_interface->url, $company_interface->public_key . $company_interface->privace_key, $param, $company_interface->protocol);
                if(!$result){
                    $this->setError('订单发货失败，或已发货');
                    return false;
                }
                $model = new CompanyOrder();
                $model->user_id = $user_id;
                $model->order_id = intval($order_id);
                $model->user_name = $user->username;
                $model->car_id = $car_id;
                $model->car_name = $car_name;
                $model->status = 1;
                $model->create_time = time();
                $model->end_time = 0;
                $model->order_pay = $order_pay;
                $model->car_bnto = $user_id . time();
                $model->order_money = $order_money;
                $model->company_id = $company_category_id;
                $model->member_name = $member_name;
                
                if($model->save())
                {
                    /* 做发货记录 */
                    $log_text = '订单发货成功';
                    $behavior = '发货';
                    $result_success = 'success';
                    $log = '记录发货出错';
                    $this->addLog($order_id, $log_text, $behavior, $result_success, $log);
                    
                    return true;
                }
                else
                {
                    $this->setError($model->getErrors());
                    return false;
                    $this->setError('订单发货详情表创建失败');
                    return false;
                }
            }
            /* 判断订单 */
        //    $order=Order::findOne(['order_id'=>$order_id,'ship_status'=>['0','1']]);
            $order = Yii::$app->api->request('basic/getOrder', ['order_id' => $order_id]);
            if($order['ret'] == 0)
            { 
                $order = $order[0];
            }else{
                $this->setError('订单信息获取失败或者订单已完成配送');
                return false;
            }
            if(!in_array($order['ship_status'], ['0', '1']))
            {
                $this->setError('订单状态非正常');
                return false;
            }

            /* 判断订单记录 */
            $delivery = OrderDelivery::findOne(['order_id' => $order_id, 'batch_status' => 1]);
            if($delivery)
            {
                $this->setError('该订单发货中...');
                return false;
            }
            /* 判断批次 */
            $batch = OrderBatch::find()->andWhere(['user_id' => $user_id, 'status' => 1])->one();
            if(!$batch || $batch->batch_wms != '0')
            {
                $this->setError('批次信息获取失败或者已经发车');
                return false;
            }
            
            $model = new OrderDelivery();
            $model->user_id = $user_id;
            $model->order_id = $order_id;
            $model->member_id = $order['member_id'];
            $model->car_id = $batch->car_id;
            $model->status = 1;
            $model->scan_time = $_SERVER['REQUEST_TIME'];
            $model->depart_time = 0;
            $model->sign_for_time = 0;
            $model->batch_no = $batch->batch_no;
            $model->batch_status = 1;
            $pay = MathHelper::sub(array($order['total_amount'], $order['payed'], $order['pmt_amount_platform'], $order['use_coins'] / 100));
            $model->pay_sign_status = $pay == 0 ? 0 : 1;
            
            if($model->save())
            {
                /* 做发货记录 */
                $log_text = '订单发货成功';
                $behavior = '发货';
                $result = 'success';
                $log = '记录发货出错';
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
     * 签收（改版后）
     * @param int $order_id 订单号
     * @param string $payMent 待收货款
     * @param int $is_paySign 是否确认收款  1：未确认 2：确认
     * 
     *  */
    public function updatePaysign($order_id, $payMent, $is_paySign, $user_id, $is_cooperation, $company_category_id)
    {
        if($is_cooperation == 0){
            $company_interface = CompanyInterface::findOne(['company_id' => $company_category_id, 'module_id' => 12]);
            if(!$company_interface){
                $this->setError('请先添加接口');
                return false;
            }
            $user = User::findOne($user_id);
            $param = [
                'order_is' => $order_id,
            ];
            $http = HttpCurlService::instance();
            $result = $http->request($company_interface->url, $company_interface->public_key . $company_interface->privace_key, $param, $company_interface->protocol);
            if(!$result){
                $this->setError('签收失败');
                return false;
            }
            $order = CompanyOrder::findOne(['order_id' => $order_id, 'user_id' => $user_id]);
            if(!$order){
                $this->setError('订单不存在');
                return false;
            }
            $order->status = 2;
            $order->save();
            $order->end_time  = time();
            return $result = '签收成功';
        }
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