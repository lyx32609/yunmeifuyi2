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


class ShopService extends Service
{
	private  $getShop = 'shop/getByPhone';
    /**
     * 获取店铺详情
     * @param int $shop_id 店铺ID
     * @return array
     */
    public function getShopDetails($shop_id,$type)
    {
        $data=\Yii::$app->api->request('shop/shopDetails',[
            'shop_id'=>$shop_id,
            'type'=>$type,
        ]);
        if($data['ret']===0)
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
    
    public function getShop($mobile)
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