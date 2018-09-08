<?php
namespace app\services;

use app\foundation\Service;
use app\models\Member;
use benben\helpers\RangeHelper;
use app\models\OrderBatch;
use app\models\OrderDelivery;
class NeighborsService extends Service
{
    /**
     * 获取周围店铺列表
     */
    public function getAroundStoreList($params = [])
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
                return $result['result'];
                
            }else{
                $this->setError('数据获取失败');
                return false;
            }

    }
    
    /**
     * 获取周围供应商列表
     */
    public function getAroundSupplierList($params = [])
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
        $data=[
            'longitude'=>$longitude,
            'latitude'=>$latitude,
            'range'=>$range,
            'page'=>$page,
            'perPage'=>$perPage,
        ];
        $result=\Yii::$app->api->request('basic/getAroundSupplier',['data'=>json_encode($data)]);
        if($result['ret']===0)
        {
            return $result['result'];
        
        }else{
            $this->setError('数据获取失败');
            return false;
        }
    }
    
    
}