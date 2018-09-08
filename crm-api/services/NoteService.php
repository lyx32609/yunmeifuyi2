<?php
namespace app\services;

use app\foundation\Service;
use app\models\UserLocation;
use app\benben\DateHelper;
use app\models\ShopNote;
use app\models\UserBusiness;
use app\models\UserBusinessNotes;
use app\models\User;

/**
 * @author Pulo
 *
 */
class NoteService extends Service
{

    /**
     * @param unknown $shopId 商家id
     * @param unknown $content 备注内容
     * @param int $shopid 店铺ID text $conte：提交的内容  varchar $user：提交人   decimal $longitude：提交人经度    decimal $latitude：提交人纬度    varchar imag：{http：\\...,第二张，第三张}
     */
    public function add($shopId,$conte,$user,$longitude,$latitude,$imag,$belong)
    {
        if(!$shopId){
            $this->setError('店铺不存在!');
            return false;
        }
        if(!$conte){
            $this->setError('内容不能为空!');
            return false;
        }
        if(!$longitude){
            $this->setError('经度不能为空!');
            return false;
        }
        if(!$latitude){
            $this->setError('纬度不能为空!');
            return false;
        }

        $columns=array(
            'shop_id'=>$shopId,
            'note' => $conte,
            'time'=>time(),
            'conte' => $conte,
            'user' => $user,
            'longitude' => $longitude,
            'latitude' => $latitude,
            'imag' => $imag,
            'belong'=>$belong,
        );
        $rs=\Yii::$app->dbofficial->createCommand()->insert('off_shop_note', $columns)->execute();
       if(!rs)
       {
           $this->setError('添加失败!');
           return false;
       }
       else 
       {
           if($belong=='1')
           {
               $shop =\Yii::$app->api->request('basic/getMember',['member_id'=>$shopId]);
               
               if(!$shop)
               {
                   $this->setError('采购商信息错误');
                   return false;
               }
               if($shop['ret']===0)
               {
                    $name=$shop[0]['shopname'];                  
               }else{
                   $this->setError('接口返回错误：'.$shop['msg']);
                   return false;
               }
               
           }elseif($belong=='2')
           {
               $supplier=\Yii::$app->api->request('basic/getSupplier',['supplierId'=>$shopId]);
               if(!$supplier)
               {
                   $this->setError('供货商信息错误');
                   return false;
               }
               if($supplier['ret']===0)
               {
                   $name=$supplier[0]['company_name'];
               }else{
                   $this->setError('接口返回错误：'.$supplier['msg']);
                   return false;
               }        
           }
           
           $user_location = new UserLocation();
           $user_location->shop_id = $shopId;
           $user_location->name = isset($name)?$name:'';
           $user_location->longitude = $longitude;
           $user_location->latitude = $latitude;
           $user_location->user = $user;
           $user_location->time = time();
           $user_location->type = 0;
           $user_location->domain = $shop['domain']?$shop['domain']:16;
           $user_location->belong=$belong;
           if (!$user_location->save())
           {
               $this->setError('添加失败!');
               return false;
           }
           
       }
       return true;
    }
    
    
    
    /**
     * @param unknown $shopId 商家id   2.1版本添加接口  添加字段是否合理 reasonable
     * @param unknown $content 备注内容
     * @param int $shopid 店铺ID text $conte：提交的内容  varchar $user：提交人   decimal $longitude：提交人经度    decimal $latitude：提交人纬度    varchar imag：{http：\\...,第二张，第三张}
     */
    public function addReasonable($shopId,$conte,$user,$longitude,$latitude,$imag,$belong)
    {
    	if(!$shopId){
    		$this->setError('店铺不存在!');
    		return false;
    	}
    	if(!$conte){
    		$this->setError('内容不能为空!');
    		return false;
    	}
    	if(!$longitude){
    		$this->setError('经度不能为空!');
    		return false;
    	}
    	if(!$latitude){
    		$this->setError('纬度不能为空!');
    		return false;
    	}

    	$columns=array(
    			'shop_id'=>$shopId,
    			'note' => $conte,
    			'time'=>time(),
    			'conte' => $conte,
    			'user' => $user,
    			'longitude' => $longitude,
    			'latitude' => $latitude,
    			'imag' => $imag,
    			'belong'=>$belong,
    	);
    	$rs=\Yii::$app->dbofficial->createCommand()->insert('off_shop_note', $columns)->execute();
    	if(!rs)
    	{
    		$this->setError('添加失败!');
    		return false;
    	}
    	else
    	{
    		if($belong=='1')
    		{
    			$shop =\Yii::$app->api->request('basic/getMember',['member_id'=>$shopId]);
    			 
    			if(!$shop)
    			{
    				$this->setError('采购商信息错误');
    				return false;
    			}
    			if($shop['ret']===0)
    			{
    				$name=$shop[0]['shopname'];
    			}else{
    				$this->setError('接口返回错误：'.$shop['msg']);
    				return false;
    			}
    			 
    		}elseif($belong=='2')
    		{
    			$supplier=\Yii::$app->api->request('basic/getSupplier',['supplierId'=>$shopId]);
    			if(!$supplier)
    			{
    				$this->setError('供货商信息错误');
    				return false;
    			}
    			if($supplier['ret']===0)
    			{
    				$name=$supplier[0]['company_name'];
    			}else{
    				$this->setError('接口返回错误：'.$supplier['msg']);
    				return false;
    			}
    		}
    		 
    		$user_location = new UserLocation();
    		$user_location->shop_id = $shopId;
    		$user_location->name = isset($name)?$name:'';
    		$user_location->longitude = $longitude;
    		$user_location->latitude = $latitude;
    		$user_location->user = $user;
    		$user_location->time = time();
    		$user_location->type = 0;
    		$user_location->domain = $shop[0]['domain']?$shop[0]['domain']:16;
    		$user_location->belong=$belong;
    		//计算定位是否合理 俩点之间的经纬度距离小于80M为合理
    		$point1 = array('lat' => $shop[0]['latitude'], 'long' => $shop[0]['longitude']);
    		$point2 = array('lat' => $latitude, 'long' => $longitude);
    		$distance = $this->getDistanceBetweenPointsNew($point1['lat'], $point1['long'], $point2['lat'], $point2['long']);
    		if($distance<80){
    		    $user_location->reasonable = '合理';
    		}else{
    		    $user_location->reasonable = '不合理';
    		}
    		
    		$username = User::find()->select('name')->where(['username'=>$user])->asArray()->one();
    		//print_r($username);exit();
    	   $user_location->username = $username['name'];
    		
    	//	print_r($user_location);exit();
/*     		$shop_longitude = sprintf("%.3f",substr(sprintf("%.4f", $shop[0]['longitude']), 0,-1));
    		$shop_latitude =  sprintf("%.3f",substr(sprintf("%.4f", $shop[0]['latitude']), 0,-1));
    		$res_longitude = sprintf("%.3f",substr(sprintf("%.4f", $longitude), 0,-1));
    		$res_latitude = sprintf("%.3f",substr(sprintf("%.4f", $latitude), 0,-1));
    		if ((abs($shop_longitude-$res_longitude))>0||(abs($shop_latitude-$res_latitude))>0){
    			$user_location->reasonable = 2;
    		}else {
    			$user_location->reasonable = 1;
    		} */
    		
    		if (!$user_location->save())
    		{
    			$this->setError('添加失败!');
    			return false;
    		}
    		 
    	}
    	return true;
    }
    
    
    
    
    
    
    /* 
     * 查询业务人员当日汇报的提交情况
     *  早上 8:30   晚上 5：30 （10月至次年4月）   6:00  （5月至次年9月）
     *  */
    public function select($user_id)
    {
        $s=0;
        $user=\app\models\User::findOne(['id'=>$user_id]);
        if(!$user)
        {
            $this->setError('用户不存在');
            return false;
        }
        $time=$_SERVER['REQUEST_TIME'];
        $today_start=DateHelper::getTodayStartTime();
        $today_end=DateHelper::getTodayEndTime();
        $real_time=$time-$today_start;   //获取当天的实时时间戳
        $goToWork=$today_start+8.5*3600;
        $m=DateHelper::getMonth();
        if($m>=5&&$m<=9)
        {
            $goOffWord=$today_start+18*3600;
        }else{
            $goOffWord=$today_start+17.5*3600;
        }
        
        $start_note=ShopNote::find()->andWhere(['user'=>$user->username])->andWhere(['between','time',$today_start,$goToWork])->one();            
        $start_user_business=UserBusiness::find()->andWhere(['staff_num'=>$user->username])->andWhere(['between','time',$today_start,$goToWork])->one();            
        $start_user_business_notes=UserBusinessNotes::find()->andWhere(['staff_num'=>$user->username])->andWhere(['between','time',$today_start,$goToWork])->one();           
        if(!$start_note&&!$start_user_business&&!$start_user_business_notes)
        {
            $s=$s+1;
            if($time-$goToWork>0)
            {
                $note=ShopNote::find()->andWhere(['user'=>$user->username])->andWhere(['between','time',$today_start,$goOffWord])->one();
                $user_business=UserBusiness::find()->andWhere(['staff_num'=>$user->username])->andWhere(['between','time',$today_start,$goOffWord])->one();
                $user_business_notes=UserBusinessNotes::find()->andWhere(['staff_num'=>$user->username])->andWhere(['between','time',$today_start,$goOffWord])->one();
                if($note||$user_business||$user_business_notes)
                {
                     $s=$s-1;
                }  
            }
            
        }
        
        if($time-$goOffWord>0)
        {
            $end_note=ShopNote::find()->andWhere(['user'=>$user->username])->andWhere(['between','time',$goOffWord,$today_end])->one();
            $end_user_business=UserBusiness::find()->andWhere(['staff_num'=>$user->username])->andWhere(['between','time',$goOffWord,$today_end])->one();
            $end_user_business_notes=UserBusinessNotes::find()->andWhere(['staff_num'=>$user->username])->andWhere(['between','time',$goOffWord,$today_end])->one();
            if(!$end_note&&!$end_user_business&&!$end_user_business_notes)
            {
                $s=$s+2;
            }
        }
        return $s;
    }
    
    
    
    //获取两个经纬度之间的距离
    function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2) {
        $theta = $longitude1 - $longitude2;
        $miles = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return $meters;
    }
}