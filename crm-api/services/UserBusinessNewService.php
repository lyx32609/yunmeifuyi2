<?php
namespace app\services;

use app\foundation\Service;
use app\models\CompanyShopNote;
use app\models\User;
use app\models\UserBusiness;
use app\models\UserBusinessSave;
use app\models\UserBusinessNotes;
use benben\helpers\MathHelper;
use app\models\UserLocation;
use app\models\Shop;
use yii\data\Pagination;
use app\models\CompanyBusinessNotes;
class UserBusinessNewService extends Service
{
    /**
     * 业务保存
     * @param 
     * @return msg："保存成功",result 
     */
    public function add($customer_name, $customer_user, $customer_tel, $customer_type, $customer_source, $customer_state, $customer_priority, $customer_longitude, $customer_latitude, $customer_photo_str, $customer_business_title, $customer_business_describe, $staff_num, $is_cooperation, $company_category_id, $shop_addr)
    {
        if(!$customer_name)
        {
            $this->setError('客户名称不能为空!');
            return false;
        }
        if(!$customer_tel)
        {
            $this->setError('客户电话不能为空!');
            return false;
        }
        if(!$customer_type)
        {
            $this->setError('客户类型不能为空!');
            return false;
        }
        if(!$customer_source)
        {
            $this->setError('客户来源不能为空!');
            return false;
        }
        if(!$customer_state)
        {
            $this->setError('客户状态不能为空!');
            return false;
        }
        if(!$customer_priority)
        {
            $this->setError('客户优先级不能为空!');
            return false;
        }
        if (!$customer_longitude || !$customer_latitude)
        {
            $this->setError('经纬度不能为空');
            return false;
        }
        if(!$customer_business_title)
        {
            $this->setError('业务标题不能为空!');
            return false;
        }
        if(!$customer_business_describe)
        {
            $this->setError('业务描述不能为空!');
            return false;
        }
        $user = User::findOne($staff_num);
        if(!$user){
            $this->setError('无此业务员!');
            return false;
        }
        $domain_id = $user->domain_id;
        if($is_cooperation == 0){
          $customer = Shop::find()
                    ->andWhere("shop_name like '%$customer_name%' and shop_domain = $domain_id and company_category_id = $company_category_id")
                    ->asArray()
                    ->all();
          $time = time();
          if ($customer)
          {
              foreach ($customer as $cus)
              {
                  $longitude = $cus['shop_longitude'] - $customer_longitude;
                  $latitude = $cus['shop_latitude'] - $customer_latitude;
                  if (abs($longitude) < 0.001 && abs($latitude) < 0.001)
                  {
                      $this->setError('客户已存在!');
                      return false;
                  }
              }
          }

          $columns = array(
            'shop_name' => $customer_name,
            'name' => $customer_user,
            'phone' => $customer_tel,
            'shop_type' => $customer_type,
            'shop_source' => $customer_source,
            'shop_status' => $customer_state,
            'shop_priority' => $customer_priority,
            'shop_longitude' => $customer_longitude,
            'shop_latitude' => $customer_latitude,
            'shop_image' => $customer_photo_str,
            'createtime' => $time,
            'shop_domain' => $domain_id,
            'user_name' => $user->username,
            'user_id' => intval($user->id),
            'company_category_id' => $company_category_id,
            'shop_review' => 2,
            'shop_addr' => $shop_addr,
            'shop_title' => $customer_business_title,
            'shop_describe' => $customer_business_describe,
          );
          $rs = \Yii::$app->dbofficial->createCommand()->insert('off_shop', $columns)->execute();
          $shop_id = \Yii::$app->dbofficial->getLastInsertID();
          // $shop_id = $rs->attributes['id'];
          if(!$rs){
              $this->setError('保存失败!');
              return false;
          } else  {
             $notes = new CompanyBusinessNotes();
             $notes->business_id = \Yii::$app->dbofficial->getLastInsertID();
             $notes->staff_num = $user->username;
             $notes->time = $time;
             $notes->followup_text = $customer_business_describe;
             if(!$notes->save())
             {
                 $result = shop::findOne(['id' => $shop_id]);
                 $result->delete();
                 $this->setError('提交失败', $notes->errors);
                 return false;
             }
             $notes_id = $notes->attributes['id'];
             $user_location = new UserLocation();
             $user_location->name = $customer_name;
             $user_location->user_longitude = $customer_longitude;
             $user_location->user_latitude = $customer_latitude;
             $user_location->user = $user->username;
             $user_location->time = $time;
             $user_location->type = 1;
             $user_location->domain = $domain_id;
             $user_location->user_longitude = $customer_longitude;
             $user_location->user_latitude = $customer_latitude;
             if (!$user_location->save())
             {
                 $result = Shop::findOne( $shop_id);
                 $result->delete();
                 $notes = CompanyBusinessNotes::findOne( $notes_id);
                 $notes->delete();
                 $this->setError('添加失败!');
                 return false;
             }
             if ($customer_type == 1){
                 $belong = 3;
             }elseif ($customer_type ==2){
                 $belong = 2;
             }elseif ($customer_type ==3){
                 $belong = 1;
             }elseif ($customer_type ==4){
                 $belong = 4;
             }elseif ($customer_type ==6){
                 $belong = 5;
             }elseif ($customer_type ==7){
                 $belong = 6;
             }elseif ($customer_type ==8){
                 $belong = 7;
             }
//             $location_id = $user_location->attributes['id'];
//             $company_shop_note = new CompanyShopNote();
//             $company_shop_note->shop_id = $shop_id;
//             $company_shop_note->note = $customer_business_title;
//             $company_shop_note->conte = $customer_business_describe;
//             $company_shop_note->time = time();
//             $company_shop_note->user = $user->username;
//             $company_shop_note->longitude = $customer_longitude;
//             $company_shop_note->latitude = $customer_latitude;
//             $company_shop_note->imag = $customer_photo_str;
//             $company_shop_note->belong = $belong;
//             if (!$company_shop_note->save()){
//                 $result = Shop::findOne( $shop_id);
//                 $result->delete();
//                 $notes = CompanyBusinessNotes::findOne( $notes_id);
//                 $notes->delete();
//                 $location = UserLocation::findOne( $location_id);
//                 $location->delete();
//                 $this->setError('添加失败!');
//                 return false;
//             }
             return [msg => '保存成功'];
          }
            
        }
        $customer = UserBusiness::find()
                    ->andWhere("customer_name like '%$customer_name%' and domain_id = $domain_id")
                    ->asArray()
                    ->all();
        if ($customer)
        {
            foreach ($customer as $cus)
            {
                $longitude = $cus['customer_longitude'] - $customer_longitude;
                $latitude = $cus['customer_latitude'] - $customer_latitude;
                if (abs($longitude) < 0.001 && abs($latitude) < 0.001)
                {
                    $this->setError('客户已存在!');
                    return false;
                }
            }
        }
        $columns = array(
            'customer_name' => $customer_name,
            'customer_user' => $customer_user,
            'customer_tel' => $customer_tel,
            'customer_type' => $customer_type,
            'customer_source' => $customer_source,
            'customer_state' => $customer_state,
            'customer_priority' => $customer_priority,
            'customer_longitude' => $customer_longitude,
            'customer_latitude' => $customer_latitude,
            'customer_photo_str' => $customer_photo_str,
            'customer_business_title' => $customer_business_title,
            'customer_business_describe' => $customer_business_describe,
            'staff_num' => $user->username,
            'time' => time(),
            'domain_id' => $domain_id,
        );
        $rs = \Yii::$app->dbofficial->createCommand()->insert('off_user_business', $columns)->execute();
        $shop_id = \Yii::$app->dbofficial->getLastInsertID();
        if(!$rs){
            $this->setError('保存失败!');
            return false;
        }
       else
       {
           $time = time();
           $notes = new UserBusinessNotes();
           $notes->business_id = \Yii::$app->dbofficial->getLastInsertID();
           $notes->staff_num = $user->username;
           $notes->time = $time;
           $notes->followup_text = $customer_business_describe;
           if(!$notes->save())
           {
               $result = UserBusiness::findOne($shop_id);
               $result->delete();
               $this->setError('提交失败', $notes->errors);
               return false;
           }
           $notes_id = $notes->attributes['id'];
           $user_location = new UserLocation();
           $user_location->name = $customer_name;
           $user_location->longitude = $customer_longitude;
           $user_location->latitude = $customer_latitude;
           $user_location->user = $user->username;
           $user_location->user_longitude = $customer_longitude;
           $user_location->user_latitude = $customer_latitude;
           $user_location->time = $time;
           $user_location->type = 1;
           $user_location->domain = $domain_id;
           if (!$user_location->save())
           {
               $result = UserBusiness::findOne($shop_id);
               $result->delete();
               $notes = UserBusinessNotes::findOne( $notes_id);
               $notes->delete();
               $this->setError('添加失败!');
               return false;
           }
        }
          return [msg => '保存成功'];
    }
    
    /**
     * 查询新增业务清单
     * @param
     * @return msg："查询成功",result
     */
    public  function getUserBusinessList($staff_num)
    {
        $data = $this->getList($staff_num);
        return $data;
    }
    
    
    public function getList($staff_num)
    {
        $list = $this->getData($staff_num);
        if (!$list)
        {
            $this->setError('此业务员暂无新增业务!');
            return false;
        }
        $result = array();
        foreach ($list as $key=>$value)
        {
           $result[$key]['businessID'] = $list[$key]['id'];
           $result[$key]['date'] = $list[$key]['time'];
           $result[$key]['customerName'] = $list[$key]['customer_name'];
           $result[$key]['title'] = $list[$key]['customer_business_title'];
           $result[$key]['priority'] = $list[$key]['customer_priority'];
        }
        return $result;
    }
    
    /**
     * 查询新增业务详情
     * @param
     * @return msg："查询成功",result
     */
    public  function getUserBusinessDetail($businessId)
    {
        $data = $this->getDetail($businessId);
        return $data;
    }
    public function getDetail($businessId)
    {
        if(!$businessId)
        {
            $this->setError('查询id不能为空!');
            return false;
        }
        $list = $this->getUserDetail($businessId);
        if (!$list)
        {
            $this->setError('暂无新增业务!');
            return false;
        }
        $phone = MathHelper::format_phone($list['customer_tel']);
        if (!$phone)
        {
            $phone = $list['customer_tel'];
        }
        $result = array();
        $result['customerName'] = $list['customer_name'];
        $result['customer_user'] = $list['customer_user'];
        $result['cusormerTel'] = $phone;
        $result['customerType'] = $list['customer_type'];
        $result['customerSource'] = $list['customer_source'];
        $result['customerState'] = $list['customer_state'];
        $result['customerPrority'] = $list['customer_priority'];
        $result['customerLocationX'] = $list['customer_longitude'];
        $result['customerLocationY'] = $list['customer_latitude'];
        return $result;
    }
    /**
     * 业务跟进修改
     * @param
     * @return msg："修改成功",result
     */
    public function userBusinessUpdate($business_id,$customer_state,$customer_priority,$customer_longitude,$customer_latitude,$customer_photo_str,$followup_text, $user_longitude, $user_latitude)
    {
        $staff = \Yii::$app->user->id;
        $user = User::findOne($staff);
        $domainId = \Yii::$app->user->identity->domainId;
        $userBusiness = UserBusiness::find()
        ->andWhere('id = :businessId',array(':businessId'=>$business_id))
        ->andWhere('staff_num = :staff',array(':staff'=>$user['username']))
        ->andWhere('domain_id = :domainId',array(':domainId'=>$domainId))
        ->one();
        if(!$userBusiness)
        {
            $this->setError('查询无此业务');
            return false;
        }
    
        if (!$customer_longitude || !$customer_latitude)
        {
            $this->setError('经纬度不能为空');
            return false;
        }
    
        $userBusiness->customer_state = $customer_state;
        $userBusiness->customer_priority = $customer_priority;
        $userBusiness->customer_longitude = $customer_longitude;
        $userBusiness->customer_latitude = $customer_latitude;
        $userBusiness->customer_photo_str = $customer_photo_str;
    
        if(!$userBusiness->save())
        {
            $this->setError('提交失败', $userBusiness->errors);
            return false;
        }
        else
        {
            $user_location = new UserLocation();
            $user_location->name = $userBusiness['customer_name'];
            $user_location->longitude = $customer_longitude;
            $user_location->latitude = $customer_latitude;
            $user_location->user = $user['username'];
            $user_location->time = time();
            $user_location->type = 1;
            $user_location->domain = $domainId;
            $user_location->user_longitude = $user_longitude;
            $user_location->user_latitude = $user_latitude;
            if (!$user_location->save())
            {
                $this->setError('添加失败!');
                return false;
            }
        }
         
        $notes = new UserBusinessNotes();
        $notes->business_id = $business_id;
        $notes->staff_num = $userBusiness['staff_num'];
        $notes->time = time();
        $notes->followup_text = $followup_text;
        if(!$notes->save())
        {
            $this->setError('提交失败', $notes->errors);
            return false;
        }
        
        return true;
    }
    
    /**
     * 业务记录查询
     * @param
     * @return msg："查询成功",result
     */
    public function userBusinessNotes($business_id, $is_cooperation)
    {
      
        $data = $this->getNotes($business_id, $is_cooperation);
        return $data;
    }
    
    public function getNotes($business_id, $is_cooperation)
    {
        if(!$business_id)
        {
            $this->setError('业务id不能为空!');
            return false;
        }
        $notes = $this->getBusinessNotes($business_id, $is_cooperation);
        if(!$notes)
        {
            $this->setError('暂无记录!');
            return false;
        }
        
        $result = array();
        foreach ($notes as $key=>$val)
        {
            $result[$key]['historyID'] = $key+1;
            $result[$key]['historyMsg'] = $notes[$key]['followup_text'];
            $result[$key]['historyTime'] = $notes[$key]['time'];
        }
        return $result;
    }
    
    public function getBusinessNotes($business_id, $is_cooperation)
    {
        //$notes = UserBusinessNotes::findAll(['business_id'=>$business_id]);
        if($is_cooperation == 0){
            $notes = CompanyBusinessNotes::find()
                    ->andWhere('business_id = :business_id',array(':business_id'=>$business_id))
                    ->asArray()
                    ->all();
        } else {
            $notes = UserBusinessNotes::find()
                    ->andWhere('business_id = :business_id',array(':business_id'=>$business_id))
                    ->asArray()
                    ->all();
        }
        
        return $notes;
    }
    
    public function getData($staff_num)
    {
        
        $user = User::findOne($staff_num);
        if(!$user){
            $this->setError('无此业务员!');
            return false;
        }
        
        $domain_id = $user['domain_id'];
        $list = UserBusiness::find()
                ->andWhere('staff_num = :staff_num',array(':staff_num'=>$user['username']))
               // ->andWhere("customer_state != '3'")
                ->andWhere('domain_id = :domain_id',array(':domain_id'=>$domain_id))
                ->asArray()
                ->all();
        
        return $list;
    }
    
    public function getUserDetail($businessId)
    {
        $staff = \Yii::$app->user->id;
        $user = User::findOne($staff);
        $domainId = \Yii::$app->user->identity->domainId;
        $list = UserBusiness::find()
                ->andWhere('id = :businessId',array(':businessId'=>$businessId))
                ->andWhere('staff_num = :staff',array(':staff'=>$user['username']))
                ->andWhere('domain_id = :domainId',array(':domainId'=>$domainId))
                ->asArray()
                ->one();
        return $list;
    }
    /**
     * 根据客户名称模糊查询相关业务信息
     * @return array
     * @author lzk
     */
    public function getBusinessName($businessName,$type,$page=1,$pageSize=10)
    {
        if(!$businessName)
        {
            $this->setError('请输入客户名称!');
            return false;
        }
        if(!$type)
        {
           $type = 3; 
        }
        
        $domain = \Yii::$app->user->identity->domainId;
        $query = UserBusiness::find()
        ->select(['id as businessID','customer_name as customerName','customer_business_title as title','customer_type as type','customer_priority as priority','time as date'])
        ->andWhere("customer_name like '%$businessName%'")
        ->andWhere("customer_state != '3'")
        ->andWhere("customer_type = '$type'")
        ->andWhere('domain_id = :domain',[':domain'=>$domain]);
    
        $pagination = new Pagination([
            'params'=>['page'=>$page],
            'defaultPageSize' => $pageSize,
            'totalCount' => $query->count(),
        ]);
    
        $business = $query->offset($pagination->offset)
        ->limit($pagination->limit)
        ->asArray()->all();
         
        if(!$business)
        {
            $this->setError('暂无业务信息!');
            return false;
        }
        return ['list'=>$business, 'pageCount'=>$pagination->pageCount];
    }
    
    
    
   /**
   * 业务预存
   * @param:
   * @return: msg："保存成功",result
   * @version:2.1
   * @author: qizhifei
   * @date:2017年3月23日
   */

    public function save($customer_name,$customer_user,$customer_tel,$customer_type,$customer_source,$customer_state,
        $customer_priority,$customer_longitude,$customer_latitude,
        $customer_photo_str,$customer_business_title,$customer_business_describe,$staff_num,$save_id)
    {
        if(!$customer_name)
        {
            $this->setError('客户名称不能为空!');
            return false;
        }
        if (!$customer_longitude || !$customer_latitude)
        {
            $this->setError('经纬度不能为空');
            return false;
        }
        $user = User::findOne($staff_num);
        if(!$user){
            $this->setError('无此业务员!');
            return false;
        }
        $domain_id = $user['domain_id'];
    
         $customer = UserBusinessSave::find()
        ->andWhere("customer_name like '%$customer_name%' and domain_id = $domain_id")
        ->asArray()
        ->all(); 

        if ($customer && !$save_id)
        {
            foreach ($customer as $cus)
            {
                $longitude = $cus['customer_longitude']-$customer_longitude;
                $latitude = $cus['customer_latitude']-$customer_latitude;
                if (abs($longitude)<0.001 && abs($latitude)<0.001)
                {
                    $this->setError('客户已存在!');
                    return false;
                }
            }
        }
        //处理二次预存图片问题
        $customer_photo_str = str_replace(\Yii::$app->params['uploadUrl'].'/', '', $customer_photo_str);
        $data=UserBusinessSave::findOne(['id'=>$save_id]);
        $columns=array(
            'customer_name'=>$customer_name,
            'customer_user'=>$customer_user,
            'customer_tel'=>$customer_tel,
            'customer_type'=>$customer_type,
            'customer_source'=>$customer_source,
            'customer_priority'=>$customer_priority,
            'customer_state'=>$customer_state,
            'customer_longitude'=>$customer_longitude,
            'customer_latitude'=>$customer_latitude,
            'customer_photo_str'=>$customer_photo_str,
            'customer_business_title'=>$customer_business_title,
            'customer_business_describe'=>$customer_business_describe,
            'staff_num'=>$user['username'],
            'time'=>time(),
            'domain_id'=>$domain_id,
        );
        //存在为修改
        if ($data)
        {
            $rs=\Yii::$app
                ->dbofficial
                ->createCommand()
                ->update('off_user_business_save', $columns,'id = '.$save_id)
                ->execute();
        }else{
            $rs=\Yii::$app
                ->dbofficial
                ->createCommand()
                ->insert('off_user_business_save', $columns)
                ->execute();
        }
        if(!rs){
            $this->setError('保存失败!');
            return false;
        }
        return [msg=>'保存成功'];
    
    }
    
    
    
    /**
     * 查询预存业务列表
     * @param
     * @return msg："查询成功",result   customer_type:1:运营商（生产商）  2:供货商  3：采购商  4：配送商  5：店铺
     */
    public  function getUserBusinessSaveList($staff_num)
    {
        $user = User::findOne($staff_num);
        if(!$user){
            $this->setError('无此业务员!');
            return false;
        }
        
        $domain_id = $user['domain_id'];
        $list = UserBusinessSave::find()
                ->andWhere('staff_num = :staff_num',array(':staff_num'=>$user['username']))
               // ->andWhere("customer_state != '3'")
                ->andWhere('domain_id = :domain_id',array(':domain_id'=>$domain_id))
                ->asArray()
                ->all();
        if (!$list)
        {
            $this->setError('此业务员暂无新增业务!');
            return false;
        }
        $result = array();
        foreach ($list as $key=>$value)
        {
           $result[$key]['businessID'] = $list[$key]['id'];
           $result[$key]['date'] = $list[$key]['time'];
           $result[$key]['customerName'] = $list[$key]['customer_name'];
           $result[$key]['customer_type'] = $list[$key]['customer_type'];
        }
        return $result;
    }
    
    
    
    /**
    * 根据预存商家id获取当前信息
    * @param: saveId : 预存id
    * @return: array 返回当前id信息
    * @version:2.1
    * @author: qizhifei
    * @date:2017年3月24日
    */
    public function getBusinessSave($saveId){
        if(!$saveId)
        {
            $this->setError('预存id不能为空！');
            return false;
        }
        $domain = \Yii::$app->user->identity->domainId;
        $business = UserBusinessSave::find()
        ->where('id = :id',[':id'=>$saveId])
        ->asArray()
        ->one();

         if($business['customer_photo_str'] != ''){
            $customer_photo_str = explode(',', $business['customer_photo_str']);
            if(count($customer_photo_str) > 1){
                foreach ($customer_photo_str as $k =>&$v){
                    $v = \Yii::$app->params['uploadUrl'].'/'. $v;
                }
                $customer_photo_str = implode(',', $customer_photo_str);
                $business['customer_photo_str'] = $customer_photo_str;                
            }else{
                $business['customer_photo_str'] = \Yii::$app->params['uploadUrl'].'/'. $business['customer_photo_str'];
            }
        } 

        if(!$business)
        {
            $this->setError('暂无预存信息!');
            return false;
        }
        return $business;
    }

    /**
     * 业务跟进修改2.1 type改为2
     * @param
     * @return msg："修改成功",result
     */
    public function userBusinessUpdateNew($business_id,$customer_state,$customer_priority,
                                       $customer_longitude,$customer_latitude,$customer_photo_str,$followup_text)
    {
        $staff = \Yii::$app->user->id;
        $user = User::findOne($staff);
        $domainId = \Yii::$app->user->identity->domainId;
        $userBusiness = UserBusiness::find()
            ->andWhere('id = :businessId',array(':businessId'=>$business_id))
            ->andWhere('staff_num = :staff',array(':staff'=>$user['username']))
            ->andWhere('domain_id = :domainId',array(':domainId'=>$domainId))
            ->one();
        if(!$userBusiness)
        {
            $this->setError('查询无此业务');
            return false;
        }

        if (!$customer_longitude || !$customer_latitude)
        {
            $this->setError('经纬度不能为空');
            return false;
        }

        $userBusiness->customer_state = $customer_state;
        $userBusiness->customer_priority = $customer_priority;
        $userBusiness->customer_longitude = $customer_longitude;
        $userBusiness->customer_latitude = $customer_latitude;
        $userBusiness->customer_photo_str = $customer_photo_str;

        if(!$userBusiness->save())
        {
            $this->setError('提交失败', $userBusiness->errors);
            return false;
        }
        else
        {
            $user_location = new UserLocation();
            $user_location->name = $userBusiness['customer_name'];
            $user_location->longitude = $customer_longitude;
            $user_location->latitude = $customer_latitude;
            $user_location->user = $user['username'];
            $user_location->time = time();
            $user_location->type = 2;
            $user_location->domain = $domainId;
            if (!$user_location->save())
            {
                $this->setError('添加失败!');
                return false;
            }
        }

        $notes = new UserBusinessNotes();
        $notes->business_id = $business_id;
        $notes->staff_num = $userBusiness['staff_num'];
        $notes->time = time();
        $notes->followup_text = $followup_text;
        if(!$notes->save())
        {
            $this->setError('提交失败', $notes->errors);
            return false;
        }

        return true;
    }
    
    
    
    //同步数据
    public function synchronization(){
        $timedata['stime'] = date('Y-m-d H:i:s',time());
        $user_location = new UserLocation();
        //[1005,160061,160087,1002,1013,1014,160075,160092,160088,160091,1004,160060,160170,160057,160205]
        $ss = $user_location::find()
   //     ->where(['username'=>null,'reasonable'=>null,'user'=>[1005,160061,160087,1002,1013,1014,160075,160092,160088,160091,1004,160060,160170,160057,160205]])
        ->where(['user'=>[160057]])
        ->andWhere(['>','time',1490976000])
        ->andWhere(['<','time',1492617600])
        ->limit('300')
        ->orderBy('id asc')->asArray()->all();
        
    //    print_r($ss);exit();
        $arrdata = array();
        foreach ($ss as $v){
            //$userlocation = $user_location::find()->where(['id' => $v['id']])->one();
            $shopId = $v['shop_id'];
            $shop =\Yii::$app->api->request('basic/getMember',['member_id'=>$shopId]);
            $point1 = array('lat' => $shop[0]['latitude'], 'long' => $shop[0]['longitude']);
            $point2 = array('lat' => $v['latitude'], 'long' => $v['longitude']);
            $distance = $this->getDistanceBetweenPointsNew($point1['lat'], $point1['long'], $point2['lat'], $point2['long']);
            if($distance<80){
                $columns['reasonable'] = '合理';
            }else{
                $columns['reasonable'] = '不合理';
            }
            //print_r($columns);exit();
            $username = User::find()->select('name')->where(['username'=>$v['user']])->asArray()->one();
            //print_r($username);exit();
            $columns['username'] = $username['name'];
            $arrdata[$v['id']]['reasonable'] = $columns['reasonable'];
            $arrdata[$v['id']]['username'] = $username['name'];
            //\Yii::$app->dbofficial->createCommand()->update('off_user_location', $columns,['id'=>$v['id']])->execute();
            
            //批量修改s
            $reasonable = '"'.$columns['reasonable'].'"';
            $reasonablesql .= sprintf("WHEN %d THEN %s ", $v['id'], $reasonable);
            $username = '"'.$columns['username'].'"';
            $usernamesql .= sprintf("WHEN %d THEN %s ", $v['id'], $username);
            
           //批量修改e
        }
        //批量修改s
        $ids = implode(',', array_keys($arrdata));
        $sql = "UPDATE off_user_location SET reasonable = CASE id ";
        $sql .= $reasonablesql;
/*         foreach ($arrdata as $id => $ordinal) {
            $reasonable = '"'.$ordinal['reasonable'].'"';
            $sql .= sprintf("WHEN %d THEN %s ", $id, $reasonable);
        } */
        $sql .= 'END,username = CASE id ';
        $sql .= $usernamesql;
/*         foreach ($arrdata as $id => $ordinal) {
            $username = '"'.$ordinal['username'].'"';
            $sql .= sprintf("WHEN %d THEN %s ", $id, $username);
        } */
        $sql .= "END WHERE id IN ($ids)";
        \Yii::$app->dbofficial->createCommand($sql)->execute();
        //批量修改e
        
        $timedata['etime'] = date('Y-m-d H:i:s',time());
        return $timedata;
    }
    //同步数据
    public function synchronization1(){
        $timedata['stime'] = date('Y-m-d H:i:s',time());
        $user_location = new UserLocation();
        //[1005,160061,160087,1002,1013,1014,160075,160092,160088,160091,1004,160060,1600170,160057,160205]
        $ss = $user_location::find()->where(['username'=>'','reasonable'=>'','user'=>1005])->limit('200')->orderBy('id asc')->asArray()->all();
        $arrdata = array();
        foreach ($ss as $k => $v){
            //$userlocation = $user_location::find()->where(['id' => $v['id']])->one();
            $shopId = $v['shop_id'];
            $shop =\Yii::$app->api->request('basic/getMember',['member_id'=>$shopId]);
            $point1 = array('lat' => $shop[0]['latitude'], 'long' => $shop[0]['longitude']);
            $point2 = array('lat' => $v['latitude'], 'long' => $v['longitude']);
            $distance = $this->getDistanceBetweenPointsNew($point1['lat'], $point1['long'], $point2['lat'], $point2['long']);
            if($distance<80){
                $columns['reasonable'] = '合理';
            }else{
                $columns['reasonable'] = '不合理';
            }
            $username = User::find()->select('name')->where(['username'=>$v['user']])->asArray()->one();
            //print_r($username);exit();
            $columns['username'] = $username['name'];
            $arrdata[$k]['reasonable'] = $columns['reasonable'];
            $arrdata[$k]['username'] = $username['name'];
            $arrdata[$k]['id'] = $v['id'];
        }
        //批量修改s
        //print_r($arrdata);exit();
        if(count($arrdata)){
            for($i =0; $i<count($arrdata);$i++){
                $arr[] = '('.$arrdata[$i]['id'].',"'.$arrdata[$i]['reasonable'].'","'.$arrdata[$i]['username'].'")';
            }
            $productidstr = implode(",",$arr);
        }
       // print_r($productidstr);exit();
        $sql = "insert into off_user_reasonable values $productidstr" ;
        \Yii::$app->dbofficial->createCommand($sql)->execute();
        //批量修改e
        $sql = 'update off_user_location as a, off_user_reasonable as b set a.reasonable=b.reasonable,a.username=b.username where a.id=b.id ';
        \Yii::$app->dbofficial->createCommand($sql)->execute();
        
        
        $timedata['etime'] = date('Y-m-d H:i:s',time());
        return $timedata;
    }
    
    //获取两个经纬度之间的距离
    public function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2) {
       // echo $latitude1."~",$longitude1."~",$latitude2."~",$longitude2;        exit();

        $theta = $longitude1 - $longitude2;
        $miles = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return $meters;
    }
    /**
     * 业务跟进修改
     * @param
     * @return msg："修改成功",result
     */
    public function userBusinessUpdateNewOne($business_id,$customer_state,$customer_priority,$customer_longitude,$customer_latitude,$customer_photo_str,$followup_text, $user_longitude, $user_latitude, $is_cooperation)
    {
        $staff = \Yii::$app->user->id;
        $user = User::findOne($staff);
        $domainId = \Yii::$app->user->identity->domainId;
        if (!$customer_longitude || !$customer_latitude)
        {
            $this->setError('经纬度不能为空');
            return false;
        }
        if($is_cooperation == 0){
            $userBusiness = Shop::find()
                    ->andWhere('id = :businessId',array(':businessId'=>$business_id))
                    ->andWhere('user_name = :staff',array(':staff'=>$user->username))
                    ->andWhere('shop_domain = :domainId',array(':domainId'=>$domainId))
                    ->one();
            if(!$userBusiness)
            {
                $this->setError('查询无此业务');
                return false;
            }
            $userBusiness->shop_status = $customer_state;
            $userBusiness->shop_priority = $customer_priority;
            $userBusiness->shop_longitude = $customer_longitude;
            $userBusiness->shop_latitude = $customer_latitude;
            $userBusiness->shop_image = $customer_photo_str;
            $userBusiness->user_name = $user->username;
            if(!$userBusiness->save())
            {
                $this->setError('提交失败', $userBusiness->errors);
                return false;
            }
           
        } else {
            $userBusiness = UserBusiness::find()
                    ->andWhere('id = :businessId',array(':businessId'=>$business_id))
                    ->andWhere('staff_num = :staff',array(':staff'=>$user->username))
                    ->andWhere('domain_id = :domainId',array(':domainId'=>$domainId))
                    ->one();
            if(!$userBusiness)
            {
                $this->setError('查询无此业务');
                return false;
            }
            $userBusiness->customer_state = $customer_state;
            $userBusiness->customer_priority = $customer_priority;
            $userBusiness->customer_longitude = $customer_longitude;
            $userBusiness->customer_latitude = $customer_latitude;
            $userBusiness->customer_photo_str = $customer_photo_str;
            $userBusiness->staff_num = $user->username;
            if(!$userBusiness->save())
            {
                $this->setError('提交失败', $userBusiness->errors);
                return false;
            }
        }
        
        
            $user_location = new UserLocation();
            if($is_cooperation == 0){
               $user_location->name = $userBusiness['shop_name']; 
            }else {
                $user_location->name = $userBusiness['customer_name'];
            }
            
            $user_location->longitude = $customer_longitude;
            $user_location->latitude = $customer_latitude;
            $user_location->user = $user->username;
            $user_location->time = time();
            $user_location->type = 1;
            $user_location->domain = $domainId;
            $user_location->user_longitude = $user_longitude;
            $user_location->user_latitude = $user_latitude;
            if (!$user_location->save())
            {
                $this->setError('添加失败!');
                return false;
            }
         if($is_cooperation == 0) {
             $notes = new CompanyBusinessNotes();
             $notes->business_id = $business_id;
             $notes->staff_num = $userBusiness['user_name'];
             $notes->time = time();
             $notes->followup_text = $followup_text;
             if(!$notes->save())
             {
                 $this->setError('提交失败', $notes->errors);
                 return false;
             }
             
             return true;
         } 
        $notes = new UserBusinessNotes();
        $notes->business_id = $business_id;
        $notes->staff_num = $userBusiness['staff_num'];
        $notes->time = time();
        $notes->followup_text = $followup_text;
        if(!$notes->save())
        {
            $this->setError('提交失败', $notes->errors);
            return false;
        }
    
        return true;
    }
    
}