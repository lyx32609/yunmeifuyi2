<?php
namespace app\services;

use app\foundation\Service;
use app\models\Order;
use app\models\Member;
use app\benben\DateHelper;
use app\models\ShopNote;
use yii\data\Pagination;
use app\models\UserSupplier;
use app\models\Users;
use app\models\Shop;
use app\models\UserDomain;
use app\models\UserLocation;


class ShopNewService extends Service
{
    private  $getShop = 'shop/getByPhone';
    /**
     * 获取店铺详情（改版后）
     * @param int $shop_id 店铺ID
     * @return array
     */
    public function getShopDetails($shop_id, $type, $is_cooperation)
    {
        if($is_cooperation == 0){
            if($type == 0){
                //3采购
                $type_status = 3;
            } else if($type == 1){
                //2供货
                $type_status = 2;
            }else if($type == 2){
                //1生产
                $type_status = 1;
            }else if($type == 3){
                //4配送
                $type_status = 4;
            }else if($type == 4){
                //6运营
                $type_status = 6;
            }else if($type == 5){
                //7销售
                $type_status = 7;
            }else if($type == 6){
                //8服务
                $type_status = 8;
            }
            $shop = Shop::find()
                    ->select(['id', 'shop_name', 'shop_latitude', 'shop_longitude'])
                    ->where(['id' => $shop_id])
                    ->andWhere(['shop_type' => $type_status])
                    ->asArray()
                    ->one();
            if(!$shop){
                $this->setError('店铺不存在');
                return false;
            }
            $location = UserLocation::find()
                    ->select(['time','user'])
                    ->where(['shop_id' => $shop['id']])
                    ->andWhere(['type' => $type])
                    ->orderBy('time desc')
                    ->asArray()
                    ->one();
            if(!$location){
                $location['time'] = '';
                $location['user_name'] = '';
            }
            $result['latitude'] = $shop['shop_latitude'];
            $result['longitude'] = $shop['shop_longitude'];
            $result['shop_id'] = $shop['id'];
            $result['shopname'] = $shop['shop_name'];
            $result['visitdate'] = $location['time'] ? $location['time'] : '';
            $result['visitUser'] = $location['user_name'] ? $location['user_name'] : '';
            return [$result];

        }
        $data = \Yii::$app->api->request('shop/shopDetails',[
            'shop_id'=>$shop_id,
            'type'=>$type,
        ]);
        if($data['ret'] === 0)
        {
            return $data['result'];
        }else{
            $this->setError('获取数据失败');
            return false;
        }
    }
 
    /**
     * 我的店铺
     * @return array
     */
    public function getMyShops($user)
    {
    
        $data = $this->getShops($user);
        return $data;
    }
    
    
    /**
     * 店铺查询
     * @return array
     */
    public function getShops($user)
    {
        if(!$user)
        {
            $this->setError('员工不存在！');
            return false;
        }
        $data = $this->shops($user);
         
        return $data;
    }
     
   /**
     * 根据名称模糊查询相关店铺
     * @return array
     * @author lzk
     */
    public function getShopsName($shopName,$type,$page=1,$pageSize=10,$join='')
    {
        if(!$shopName)
        {
            $this->setError('请输入名称!');
            return false;
        }
        if(!$type)
        {
            $type = 0;
        }
        $domain = \Yii::$app->user->identity->domainId;
        $result=\Yii::$app->api->request('shop/getShops',[
            'shopName'=>$shopName,
            'type'=>$type,
            'page'=>$page,
            'pageSize'=>$pageSize,
            'join'=>$join,
            'domain'=>$domain,
        ]);
        if($result['ret']===0)
        {
            return $result['result'];
        }else{
            $this->setError('查询失败');
            return false;
        }
    }
    /* 
     * 根据用户名 查找店铺信息
     * @ return
     *  */
    
    public function getShop($mobile, $is_cooperation, $company_category_id)
    {
        if(!$mobile)
        {
           $this->setError('手机号不能为空'); 
           return false;
        }
        $domain = \Yii::$app->user->identity->domainId;
        $gather_domain = UserDomain::find()
                ->select(['domain_id'])
                ->where(['are_region_id' => $domain])
                ->asArray()
                ->one();
        if($is_cooperation == 0){
            $shop = Shop::find()
                ->select(['id', 'shop_name', 'name', 'phone', 'shop_longitude', 'shop_latitude', 'shop_addr'])
                ->andWhere(['company_category_id' => $company_category_id])
                ->andWhere(['shop_review' => 2])
                ->andWhere(['phone' => $mobile])
                ->asArray()
                ->one();
            if(!$shop){
                $this->setError(['店铺不存在']);
                return false;
            }
            if($shop['shop_domian'] == $domain){
                $shop['is_domain'] = '1';
            } else {
                $shop['is_domain'] = '0';
            }

            if($shop['shop_type'] == 5){
                $shop['identity'] = 'member';
            } else if($shop['shop_type'] == 2){
                $shop['identity'] = 'supplier';
            } else {
                $shop['identity'] = '';
            }
            return $result = [
                'id' => $shop['id'],
                'phone' => $shop['phone'],
                'address' => $shop['shop_addr'],
                'longitude' => $shop['shop_longitude'],
                'latitude' => $shop['shop_latitude'],
                'is_domain' => $shop['is_domain'],
                'identity' => $shop['identity'],
                'name' => $shop['shop_name'],
            ];
        }
        
        $postData = [
            'mobile' => $mobile,
            'domain' => $gather_domain['domain_id'],
        ];
        $data = \Yii::$app->api->request($this->getShop,$postData);
        if($data['ret'] === 0)
        { 
            $members = $data['result']['members'];
            $suppliers = $data['result']['suppliers'];
        }
        $member_array = [];
        foreach ($members as $member)
        {
            if($member['domain_id'] == $domain)
            {
                $member['is_domain'] = '1';
            }
            else 
            {
                $member['is_domain'] = '0';
            }
            unset($member['domain_id']);
            $member['identity'] = 'member';
            $member_array[] = $member;
        }
        foreach($suppliers as $supplier)
        {
            if($supplier['domain_id'] == $domain)
            {
                $supplier['is_domain'] = '1';
            }
            else
            {
                $supplier['is_domain'] = '0';
            }
            unset($supplier['domain_id']);
            $supplier['longitude'] = !empty($supplier['longitude'])?$supplier['longitude']:0;
            $supplier['latitude'] = !empty($supplier['latitude'])?$supplier['latitude']:0;            
            $supplier['identity'] = 'supplier';
            $member_array[] = $supplier;
        }
        if(!$member_array)
        {
            $this->setError('用户不存在');
            return false;
        }
        return $member_array;
    }
    
    
    
    
    
    /*
     * 根据手机号 查找店铺信息
     *@param $mobile 手机号
     *@ return 1 为存在  0为不存在
    *  */
    
    public function getIsShop($mobile)
    {
        if(!$mobile)
        {
            $this->setError('手机号不能为空');
            return false;
        }
        $domain = \Yii::$app->user->identity->domainId;
        $postData = [
        'mobile' => $mobile,
        'domain' => $domain,
        ];
        $data = \Yii::$app->api->request($this->getShop,$postData);
        if($data['ret'] === 0)
        {
            $members = $data['result']['members'];
            $suppliers = $data['result']['suppliers'];
        }
        $member_array = [];
        foreach ($members as $member)
        {
            if($member['domain_id'] == $domain)
            {
                $member['is_domain'] = '1';
            }
            else
            {
                $member['is_domain'] = '0';
            }
            unset($member['domain_id']);
            $member['identity'] = 'member';
            $member_array[] = $member;
        }
        
        if(count($member_array) > 0 && $member_array !== false){
            return 1;
        }else{
            return 0;
        }
        
    }
    
    
    
    
    
    

    
    
              
}