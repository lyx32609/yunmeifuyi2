<?php
namespace app\services;

use app\foundation\Service;
use app\models\Member;
use benben\helpers\RangeHelper;
use app\models\OrderBatch;
use app\models\OrderDelivery;
use app\models\Shop;
use app\models\User;
use app\models\CompanyInterface;
use app\models\BindCar;
use app\models\CompanyShopNote;
use app\models\ShopNote;
use app\services\HttpCurlService;
use yii\data\Pagination;
use yii\helpers\VarDumper;
class NeighborsNewService extends Service
{
    /**
     * 获取周围店铺列表
     */
    public function getAroundStoreList($params = [], $is_cooperation, $company_category_id)
    {
       
        $longitude = $params['longitude'];    //当前经度
        $latitude = $params['latitude'];      //当前纬度
        $range = $params['range'];            //指定范围
        $page = $params['page'];              //页数
        $perPage = $params['per_page'];       //每页显示数量
        $flag = $params['flag'];                //调用状态      传1过来
        if (!$longitude || !$latitude)
        {
            $this->setError('经纬度不能为空');
            return false;
        }
        if($is_cooperation == 0){
        	$user_id=\Yii::$app->user->id;
        	if(!$user_id){
        		$this->setError('请先登录');
        		return false;
        	}
        	if($flag == '1'){
                 $url =  CompanyInterface::find()
                        ->select(['url', 'public_key', 'privace_key', 'protocol'])
                        ->where(['company_id' => $company_category_id])
                        ->andWhere(['module_id' => 14])
                        ->asArray()
                        ->one();
                if(!$url){
                    $this->setError('请先添加对接接口');
                    return false;
                }
                $car_id = BindCar::findOne(['user_id' => $user_id]);
                if(!$car_id){
                    $this->setError('获取车辆信息失败');
                    return false;
                }
                if($car_id->status == 1){
                    return [
                        'ret'=>'10',
                        'msg'=>'请先发车后再操作',
                        'car' => [
                            'car_id' => $car_id->car_id,
                            'car_name' => $car_id->car_name,
                        ]
                    ];
                }
                $param = [
                    'car_id' => $car_id->car_id
                ];
                $http = HttpCurlService::instance();
                $result = $http->request($url['url'], $url['public_key'] . $url['privace_key'], $param, $url['protocol']);
                $car = [
                    'car_id' => $car_id->car_id,
                    'car_name' => $car_id->car_name,
                    'user_name' => $car_id->user_name,
                    'user_phone' => $car_id->user_phone,
                ];
                if($result){
                    return $data = [
                        'minfo' => $result,
                        'car' => $car
                    ];
                } else {
                    $this->setError('获取车辆配送店铺列表失败');
                    return false;
                }
            }
            $user = User::findOne(['id' => \Yii::$app->user->id]);
            $rectanglePoints = self::returnSquarePoint($longitude, $latitude, $range);
            $result = $this->getShopInfoByPoints($rectanglePoints, $page, $perPage, $user->domain_id, $user->company_categroy_id, 3);
            if(!$result['minfo']){
        		$this->setError('暂无店铺信息');
        		return false;
        	} 
        	$list = [];
        	for($i = 0; $i < count($result['minfo']); $i++) {
        		$list[$i]['mobile'] = $result['minfo'][$i]['phone'];
        		$list[$i]['member_id'] = $result['minfo'][$i]['id'];
        		$list[$i]['longitude'] = $result['minfo'][$i]['shop_longitude'];
        		$list[$i]['latitude'] = $result['minfo'][$i]['shop_latitude'];
        		$list[$i]['addr'] = $result['minfo'][$i]['shop_addr'];
        		$list[$i]['name'] = $result['minfo'][$i]['name'];
        		$list[$i]['shopname'] = $result['minfo'][$i]['shop_name'];
        		$list[$i]['visit_num'] = $this->getVisitNum($list[$i]['member_id'], 0);
        	} 
            $bindCars = BindCar::findOne(['user_id' => $user_id]);
            $car = [
                'car_id' => $bindCars->car_id,
                'car_name' => $bindCars->car_name,
                'user_name' => $bindCars->user_name,
                'user_phone' => $bindCars->user_phone,
            ];
        	return $data = [
        		'minfo' => $list,
        		'totalNum' => $result['totalNum'],
                'totalPage' => $result['totalPage'],
                'car' => $car,
        	];

        }
        if($flag=='1')
        {
            
            $user_id=\Yii::$app->user->id;
            $batch=OrderBatch::findOne(['user_id'=>$user_id,'status'=>1]);
            if(!$batch)
            {
                $this->setError('车次不存在');
                return false;
            }
            if($batch->batch_wms==='0')
            {
                return[
                    'ret'=>'10',
                    'msg'=>'请先发车后再操作',
                ];
            }
            $deliveries=OrderDelivery::find()->andWhere(['batch_no'=>$batch->batch_no,'status'=>2,'batch_status'=>1])->groupBy('member_id')->all();
            if(!$deliveries)
            {
                $this->setError('获取车次下订单信息失败');
                return false;
            }
            $member_batch=[];
            foreach ($deliveries as $val){
                $member_batch[]=$val->member_id;
            }
            if(empty($member_batch))
            {
                $this->setError('没有找到未签收采购商');
                return false;
            }
        }
            $data=json_encode([
                'member_batch'=>$member_batch,
                'longitude'=>$longitude, 
                'latitude'=>$latitude, 
                'range'=>$range, 
                'page'=>$page, 
                'perPage'=>$perPage,                
            ]);
            $result=\Yii::$app->api->request('basic/getAroundStores',['data'=>$data]);
            if($result['ret']===0)
            { 
                for($i = 0; $i < count($result['result']['minfo']); $i++){
                    $result['result']['minfo'][$i]['visit_num'] = $this->getVisitNum($result['result']['minfo'][$i]['member_id'], 1);
                }
                return $result['result'];
                
            }else{
                $this->setError('数据获取失败');
                return false;
            }

    }
    
    /**
     * 获取周围供应商列表
     */
    public function getAroundSupplierList($params = [], $is_cooperation, $company_category_id)
    {
        $longitude = $params['longitude'];    //当前经度
        $latitude = $params['latitude'];      //当前纬度
        $range = $params['range'];            //指定范围
        $page = $params['page'];              //页数
        $perPage = $params['per_page'];       //每页显示数量
        
        if (!$longitude || !$latitude)
        {
            $this->setError('经纬度不能为空');
            return false;
        }
        if($is_cooperation == 0) {
            $user_id=\Yii::$app->user->id;
            if(!$user_id){
                $this->setError('请先登录');
                return false;
            }
            $user = User::findOne($user_id);
            $rectanglePoints = self::returnSquarePoint($longitude, $latitude, $range);
            $result = $this->getShopInfoByPoints($rectanglePoints, $page, $perPage, $user->domain_id, $user->company_categroy_id, 2);
            if(!$result){
                $this->setError('暂无店铺信息');
                return false;
            } 
            $list = [];
            for($i = 0; $i < count($result['minfo']); $i++) {
                $list[$i]['linkman_tel'] = $result['minfo'][$i]['phone'];
                $list[$i]['longitude'] = $result['minfo'][$i]['shop_longitude'];
                $list[$i]['latitude'] = $result['minfo'][$i]['shop_latitude'];
                $list[$i]['address'] = $result['minfo'][$i]['shop_addr'];
                $list[$i]['company_name'] = $result['minfo'][$i]['shop_name'];
                $list[$i]['uid'] = $result['minfo'][$i]['id'];// 之前是user_name
                $list[$i]['visit_num'] = $this->getVisitNum($list[$i]['id'], 0);
            }
            return $data = [
                'minfo' => $list,
                'totalNum' => $result['totalNum'],
                'totalPage' => $result['totalPage']
            ];

        }
        
        $data = [
            'longitude' => $longitude,
            'latitude' => $latitude,
            'range' => $range,
            'page' => $page,
            'perPage' => $perPage,
        ];
        $result = \Yii::$app->api->request('basic/getAroundSupplier',['data' => json_encode($data)]);
        if($result['ret'] === 0)
        {
            for($i = 0; $i < count($result['result']['minfo']); $i++){
                $result['result']['minfo'][$i]['visit_num'] = $this->getVisitNum($result['result']['minfo'][$i]['uid'], 1);
            }
            return $result['result'];
        
        }else{
            $this->setError('数据获取失败');
            return false;
        }
    }
    /**
     * 获取该店铺在哪个区间存在拜访
     * @param unknown $shop_id
     * @param unknown $is_cooperation
     */
    public function getVisitNum($shop_id, $is_cooperation)
    {
//         for($i = 1; $i < 4; $i++){
//             $list[$i]['time'] = $this->getTime($i);
//             $list[$i]['num'] = $this->getNum($shop_id, $is_cooperation, $list[$i]['time']['start'], $list[$i]['time']['end']);
//             if(!$list[$i]['num'] || ($list[$i]['num'] && $i == '3')){
//                 return $i;
//             }
//         }
//         return 1;
           $list = $this->getNum($shop_id, $is_cooperation);
           $time_one = time() - 1209600;
           $time_two = time() - 2419200;
           if($list){
               if($list['time'] > $time_one){
                   return 1;
               } else {
                   if($list['time'] > $time_two){
                       return 2;
                   } else {
                       return 3;
                   }
               }
           } else {
               return 3;
           }
           
    }
    /**
     * 获取不同周期时间
     * @param unknown $type
     */
    public function getTime($type)
    {
        if($type == '1'){//获取本周开始与结束时间
            $start =  time() - 1209600;
            $end = time();
        } else if($type == '2'){//获取上周开始与结束时间
            $start = time() - 2419200;
            $end = time() - 1209600;
        } else if($type == '3'){//获取三周前开始与结束时间
            $start = 1;
            $end = time() - 2419200;
        } 
        return $list = [
            'start' => $start,
            'end' => $end
        ];
    }
    /**
     * 查询时间区间内店铺是否被拜访过
     * @param unknown $shop_id
     * @param unknown $is_cooperation
     * @param unknown $start
     * @param unknown $end
     * @return boolean
     */
    public function getNum($shop_id, $is_cooperation, $start = null, $end = null)
    {
        
        if($is_cooperation == '0') {
            $result = CompanyShopNote::find()
                    ->select(['time'])
                    ->where(['shop_id' => $shop_id])
//                     ->andWhere(['between', 'time', $start, $end])
                    ->orderBy('time desc')
                    ->asArray()
                    ->one();
            
        } else {
            $result = ShopNote::find()
                    ->select(['time'])
                    ->where(['shop_id' => $shop_id])
//                     ->andWhere(['between', 'time', $start, $end])
                    ->orderBy('time desc')
                    ->asArray()
                    ->one();
        }
        if($result) {
            return $result;
        }
        return false;
    }
    const EARTH_RADIUS = 6371;  //地球半径，平均半径为 6371km
    
    /**
     *计算某个经纬度的周围某段距离的正方形的四个点
     *
     *@param lng float 经度
     *@param lat float 纬度
     *@param distance float 该点所在圆的半径，该圆与此正方形内切，默认值为0.5千米
     *@return array 正方形的四个点的经纬度坐标
     */
    public static function returnSquarePoint($lng, $lat,$distance = 0.5){
    
        $dlng =  2 * asin(sin($distance / (2 * self::EARTH_RADIUS)) / cos(deg2rad($lat)));
        $dlng = rad2deg($dlng);
    
        $dlat = $distance/self::EARTH_RADIUS;
        $dlat = rad2deg($dlat);
    
        return array(
            'left-top'=>array('lat'=>$lat + $dlat,'lng'=>$lng-$dlng),
            'right-top'=>array('lat'=>$lat + $dlat, 'lng'=>$lng + $dlng),
            'left-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng - $dlng),
            'right-bottom'=>array('lat'=>$lat - $dlat, 'lng'=>$lng + $dlng)
        );
    }
    /**
     * 获取周围供应商列表
     * @author lzk
     * @param array $params
     */
    private  function getShopInfoByPoints($params = [], $page, $perPage, $domain_id = NULL, $company_id, $type){
        $condition = "s.shop_latitude <> 0 AND s.shop_latitude > {$params['right-bottom']['lat']} AND s.shop_latitude < {$params['left-top']['lat']} AND
        s.shop_longitude <> 0 AND s.shop_longitude > {$params['left-top']['lng']} AND s.shop_longitude < {$params['right-bottom']['lng']}";
    
        if(isset($params['ids']) && is_array($params['ids']) && !empty($params['ids']))
        {
            $ids = implode(',', $params['ids']);
            $condition .= " AND s.uid in ($ids)";
        }
    
        $sql = "
        SELECT s.id, s.name,s.shop_longitude, s.shop_latitude, s.shop_name, s.shop_addr, s.phone  FROM
        off_shop AS s  WHERE shop_domain = {$domain_id} AND company_category_id = {$company_id} AND shop_type = {$type} AND shop_review = 2";
       
        $sql_count = "
        SELECT count(*) FROM  off_shop AS s  WHERE {$condition}";
    
        return $this->selectPage($sql, $sql_count, $page, $perPage);
    }
    /**
     * 分页操作
     * @author lzk
     * @param string $sql
     * @param string $sql_count
     * @param string $page
     * @param string $perPage
     * @return array $data
     */
    private  function selectPage($sql = null, $sql_count = null, $page, $perPage){
        $data = [];
        $data['totalNum'] = \Yii::$app->dbofficial->createCommand($sql_count)->queryScalar();
        if ($perPage==0)
        {
            $perPage = $data['totalNum']?$data['totalNum']:1;
        }
        $data['totalPage'] = ceil($data['totalNum'] / $perPage);
        if($perPage == null){ 
            $data['minfo'] = \Yii::$app->dbofficial->createCommand($sql)->queryAll();
        } else {
            $perPage = ($perPage >= $data['totalNum'])? $data['totalNum']:$perPage;
            $offset = $perPage * ($page-1);
    
            if($offset >= 0 || $perPage >= 0){
                $offset = ($offset >= 0)? $offset.",":'';
                $perPage = ($perPage >= 0)? $perPage:'18446744073709551615';
                $sql .= ' LIMIT '.$offset.' '.$perPage;
            }
            $data['minfo'] = \Yii::$app->dbofficial->createCommand($sql)->queryAll();
        }
        return $data;
    }
    /**
     * 获取生产商等
     */
    public function getAroundList($params = [], $is_cooperation, $company_category_id, $type)
    {
         
        $longitude = $params['longitude'];    //当前经度
        $latitude = $params['latitude'];      //当前纬度
        $range = $params['range'];            //指定范围
        $page = $params['page'];              //页数
        $perPage = $params['per_page'];       //每页显示数量
        $flag = $params['flag'];                //调用状态      传1过来
        if (!$longitude || !$latitude)
        {
            $this->setError('经纬度不能为空');
            return false;
        }
        if($is_cooperation == 0){
            $user_id=\Yii::$app->user->id;
            if(!$user_id){
                $this->setError('请先登录');
                return false;
            }
            if($flag == '1'){
                $url =  CompanyInterface::find()
                ->select(['url', 'public_key', 'privace_key', 'protocol'])
                ->where(['company_id' => $company_category_id])
                ->andWhere(['module_id' => 14])
                ->asArray()
                ->one();
                if(!$url){
                    $this->setError('请先添加对接接口');
                    return false;
                }
                $car_id = BindCar::findOne(['user_id' => $user_id]);
                if(!$car_id){
                    $this->setError('获取车辆信息失败');
                    return false;
                }
                if($car_id->status == 1){
                    return [
                        'ret'=>'10',
                        'msg'=>'请先发车后再操作',
                        'car' => [
                            'car_id' => $car_id->car_id,
                            'car_name' => $car_id->car_name,
                        ]
                    ];
                }
                $param = [
                    'car_id' => $car_id->car_id
                ];
                $http = HttpCurlService::instance();
                $result = $http->request($url['url'], $url['public_key'] . $url['privace_key'], $param, $url['protocol']);
                $car = [
                    'car_id' => $car_id->car_id,
                    'car_name' => $car_id->car_name,
                    'user_name' => $car_id->user_name,
                    'user_phone' => $car_id->user_phone,
                ];
                if($result){
                    return $data = [
                        'minfo' => $result,
                        'car' => $car
                    ];
                } else {
                    $this->setError('获取车辆配送店铺列表失败');
                    return false;
                }
            }
            $user = User::findOne(['id' => \Yii::$app->user->id]);
            $rectanglePoints = self::returnSquarePoint($longitude, $latitude, $range);
            $result = $this->getShopInfoByPoints($rectanglePoints, $page, $perPage, $user->domain_id, $user->company_categroy_id, $type);
            if(!$result['minfo']){
                $this->setError('暂无店铺信息');
                return false;
            }
            $list = [];
            for($i = 0; $i < count($result['minfo']); $i++) {
                $list[$i]['mobile'] = $result['minfo'][$i]['phone'];
                $list[$i]['member_id'] = $result['minfo'][$i]['id'];
                $list[$i]['longitude'] = $result['minfo'][$i]['shop_longitude'];
                $list[$i]['latitude'] = $result['minfo'][$i]['shop_latitude'];
                $list[$i]['addr'] = $result['minfo'][$i]['shop_addr'];
                $list[$i]['name'] = $result['minfo'][$i]['name'];
                $list[$i]['shopname'] = $result['minfo'][$i]['shop_name'];
                $list[$i]['visit_num'] = $this->getVisitNum($list[$i]['member_id'], 0);
            }
            $bindCars = BindCar::findOne(['user_id' => $user_id]);
            $car = [
                'car_id' => $bindCars->car_id,
                'car_name' => $bindCars->car_name,
                'user_name' => $bindCars->user_name,
                'user_phone' => $bindCars->user_phone,
            ];
            return $data = [
                'minfo' => $list,
                'totalNum' => $result['totalNum'],
                'totalPage' => $result['totalPage'],
                'car' => $car,
            ];
    
        }
        if($flag=='1')
        {
    
            $user_id=\Yii::$app->user->id;
            $batch=OrderBatch::findOne(['user_id'=>$user_id,'status'=>1]);
            if(!$batch)
            {
                $this->setError('车次不存在');
                return false;
            }
            if($batch->batch_wms==='0')
            {
                return[
                    'ret'=>'10',
                    'msg'=>'请先发车后再操作',
                ];
            }
            $deliveries=OrderDelivery::find()->andWhere(['batch_no'=>$batch->batch_no,'status'=>2,'batch_status'=>1])->groupBy('member_id')->all();
            if(!$deliveries)
            {
                $this->setError('获取车次下订单信息失败');
                return false;
            }
            $member_batch=[];
            foreach ($deliveries as $val){
                $member_batch[]=$val->member_id;
            }
            if(empty($member_batch))
            {
                $this->setError('没有找到未签收采购商');
                return false;
            }
        }
        $data=json_encode([
            'member_batch'=>$member_batch,
            'longitude'=>$longitude,
            'latitude'=>$latitude,
            'range'=>$range,
            'page'=>$page,
            'perPage'=>$perPage,
        ]);
        $result=\Yii::$app->api->request('basic/getAroundStores',['data'=>$data]);
        if($result['ret']===0)
        {
            for($i = 0; $i < count($result['result']['minfo']); $i++){
                $result['result']['minfo'][$i]['visit_num'] = $this->getVisitNum($result['result']['minfo'][$i]['member_id'], 1);
            }
            return $result['result'];
    
        }else{
            $this->setError('数据获取失败');
            return false;
        }
    
    }
}